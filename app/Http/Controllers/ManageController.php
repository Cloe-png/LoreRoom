<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Chronicle;
use App\Models\Faction;
use App\Models\Chapter;
use App\Models\Place;
use App\Models\User;
use App\Models\UserLog;
use App\Models\World;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ManageController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $user = $request->user();
        $searchTerm = trim((string) $request->query('q', ''));
        $activeWorld = $this->currentWorld();
        $worldId = $this->currentWorldId();

        return view('manage.index', [
            'activeWorld' => $activeWorld,
            'activeWorldMetrics' => $this->buildActiveWorldMetrics($activeWorld),
            'searchTerm' => $searchTerm,
            'searchResults' => $searchTerm !== '' ? $this->buildSearchResults($searchTerm, $user) : [],
            'dashboardStats' => $this->buildDashboardStats($today, $user),
            'recentActivity' => $this->buildRecentActivity($today->copy()->subDays(21)),
            'continueItem' => $this->buildContinueItem($request),
            'favoritePages' => $user ? $this->buildFavoritePages($user->id) : $this->buildFavoritePages(null),
            'incompleteItems' => $this->buildIncompleteItems(),
            'worldProgress' => $this->buildWorldProgress(),
            'worldsCount' => $user ? $user->worlds()->count() : 0,
            'charactersCount' => Character::count(),
            'placesCount' => Place::count(),
            'chroniclesCount' => Chronicle::count(),
            'today' => $today,
            'todayBirthdays' => Character::with('world')
                ->whereMonth('birth_date', $today->month)
                ->whereDay('birth_date', $today->day)
                ->orderBy('name')
                ->get(),
            'todayChronicles' => Chronicle::with('world')
                ->whereDate('event_date', $today)
                ->orderBy('title')
                ->get(),
            'upcomingChronicles' => Chronicle::with('world')
                ->whereDate('event_date', '>', $today)
                ->whereDate('event_date', '<=', $today->copy()->addDays(14))
                ->orderBy('event_date')
                ->orderBy('title')
                ->get(),
            'recentChronicles' => Chronicle::with('world')->latest()->take(5)->get(),
            'recentWorlds' => $user
                ? $user->worlds()->latest('created_at')->take(5)->get()
                : collect(),
            'currentWorldId' => $worldId,
        ]);
    }

    private function buildActiveWorldMetrics(?World $world): array
    {
        if (!$world) {
            return [
                'characters' => 0,
                'places' => 0,
                'chronicles' => 0,
                'chapters' => 0,
                'summary' => null,
            ];
        }

        return [
            'characters' => Character::count(),
            'places' => Place::count(),
            'chronicles' => Chronicle::count(),
            'chapters' => Chapter::count(),
            'summary' => trim((string) $world->summary) !== '' ? $world->summary : null,
        ];
    }

    private function buildDashboardStats(Carbon $today, ?User $user): array
    {
        $startCurrent = $today->copy()->subDays(6)->startOfDay();
        $startPrevious = $today->copy()->subDays(13)->startOfDay();
        $endPrevious = $today->copy()->subDays(7)->endOfDay();

        $charactersQuery = Character::query();
        $placesQuery = Place::query();
        $chroniclesQuery = Chronicle::query();
        $factionsQuery = Faction::query();

        $charactersCurrent = (clone $charactersQuery)->count();
        $charactersCurrentWeek = (clone $charactersQuery)->whereBetween('created_at', [$startCurrent, $today->copy()->endOfDay()])->count();
        $charactersPreviousWeek = (clone $charactersQuery)->whereBetween('created_at', [$startPrevious, $endPrevious])->count();

        $placesCurrent = (clone $placesQuery)->count();
        $placesCurrentWeek = (clone $placesQuery)->whereBetween('created_at', [$startCurrent, $today->copy()->endOfDay()])->count();
        $placesPreviousWeek = (clone $placesQuery)->whereBetween('created_at', [$startPrevious, $endPrevious])->count();

        $chroniclesCurrent = (clone $chroniclesQuery)->count();
        $chroniclesCurrentWeek = (clone $chroniclesQuery)->whereBetween('created_at', [$startCurrent, $today->copy()->endOfDay()])->count();
        $chroniclesPreviousWeek = (clone $chroniclesQuery)->whereBetween('created_at', [$startPrevious, $endPrevious])->count();

        $factionsCurrent = (clone $factionsQuery)->count();
        $factionsCurrentWeek = (clone $factionsQuery)->whereBetween('created_at', [$startCurrent, $today->copy()->endOfDay()])->count();
        $factionsPreviousWeek = (clone $factionsQuery)->whereBetween('created_at', [$startPrevious, $endPrevious])->count();

        return [
            $this->buildStatCard(
                'Personnages',
                $charactersCurrent,
                $charactersCurrentWeek - $charactersPreviousWeek,
                (clone $charactersQuery)->latest('created_at')->first(),
                'display_name',
                'manage.characters.index'
            ),
            $this->buildStatCard(
                'Chroniques',
                $chroniclesCurrent,
                $chroniclesCurrentWeek - $chroniclesPreviousWeek,
                (clone $chroniclesQuery)->latest('created_at')->first(),
                'title',
                'manage.chronicles.index'
            ),
            $this->buildStatCard(
                'Lieux',
                $placesCurrent,
                $placesCurrentWeek - $placesPreviousWeek,
                (clone $placesQuery)->latest('created_at')->first(),
                'name',
                'manage.places.index'
            ),
            $this->buildStatCard(
                'Factions',
                $factionsCurrent,
                $factionsCurrentWeek - $factionsPreviousWeek,
                (clone $factionsQuery)->latest('created_at')->first(),
                'name',
                'manage.factions.index'
            ),
        ];
    }

    private function buildStatCard(
        string $label,
        int $value,
        int $delta,
        ?object $latest,
        string $nameField,
        string $routeName
    ): array {
        $latestLabel = $latest ? trim((string) ($latest->{$nameField} ?? '')) : '';
        $latestDate = $latest && isset($latest->created_at) ? $latest->created_at->locale('fr')->translatedFormat('d/m/Y') : null;

        return [
            'label' => $label,
            'value' => $value,
            'delta' => $delta,
            'latestLabel' => $latestLabel !== '' ? $latestLabel : 'Aucun élément récent',
            'latestDate' => $latestDate,
            'route' => Route::has($routeName) ? route($routeName) : null,
        ];
    }

    private function buildSearchResults(string $term, ?User $user = null): array
    {
        $like = '%' . $term . '%';

        $characters = Character::with('world')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('summary', 'like', $like);
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        $chronicles = Chronicle::with('world')
            ->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('summary', 'like', $like)
                    ->orWhere('content', 'like', $like);
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        $places = Place::with('world')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('type', 'like', $like)
                    ->orWhere('region', 'like', $like)
                    ->orWhere('summary', 'like', $like);
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        $worldsQuery = $user ? $user->worlds() : World::query()->whereRaw('1 = 0');
        $worlds = $worldsQuery
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('summary', 'like', $like);
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        $factions = Faction::with('world')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('type', 'like', $like)
                    ->orWhere('summary', 'like', $like)
                    ->orWhere('motto', 'like', $like);
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        return [
            [
                'key' => 'characters',
                'label' => 'Personnages',
                'items' => $characters->map(function (Character $character) {
                    return [
                        'label' => $character->display_name,
                        'meta' => trim(implode(' · ', array_filter([
                            $character->role ?: null,
                            optional($character->world)->name ?: null,
                        ]))),
                        'url' => route('manage.characters.show', $character),
                    ];
                })->all(),
            ],
            [
                'key' => 'chronicles',
                'label' => 'Chroniques',
                'items' => $chronicles->map(function (Chronicle $chronicle) {
                    return [
                        'label' => $chronicle->title,
                        'meta' => trim(implode(' · ', array_filter([
                            optional($chronicle->world)->name ?: null,
                            $chronicle->event_date ? $chronicle->event_date->locale('fr')->translatedFormat('d/m/Y') : null,
                        ]))),
                        'url' => route('manage.chronicles.show', $chronicle),
                    ];
                })->all(),
            ],
            [
                'key' => 'places',
                'label' => 'Lieux',
                'items' => $places->map(function (Place $place) {
                    return [
                        'label' => $place->name,
                        'meta' => trim(implode(' · ', array_filter([
                            $place->type ?: null,
                            optional($place->world)->name ?: null,
                        ]))),
                        'url' => route('manage.places.show', $place),
                    ];
                })->all(),
            ],
            [
                'key' => 'worlds',
                'label' => 'Mondes',
                'items' => $worlds->map(function (World $world) {
                    return [
                        'label' => $world->name,
                        'meta' => trim((string) $world->summary),
                        'url' => route('manage.worlds.show', $world),
                    ];
                })->all(),
            ],
            [
                'key' => 'factions',
                'label' => 'Factions',
                'items' => $factions->map(function (Faction $faction) {
                    return [
                        'label' => $faction->name,
                        'meta' => trim(implode(' · ', array_filter([
                            $faction->type ?: null,
                            optional($faction->world)->name ?: null,
                        ]))),
                        'url' => route('manage.factions.show', $faction),
                    ];
                })->all(),
            ],
        ];
    }

    private function buildRecentActivity(Carbon $since): Collection
    {
        $activities = collect();

        $typeConfigs = [
            [
                'class' => Character::class,
                'label' => 'Personnage',
                'route' => 'manage.characters.show',
                'name' => fn (Character $item) => $item->display_name,
                'world' => fn (Character $item) => optional($item->world)->name,
            ],
            [
                'class' => Chronicle::class,
                'label' => 'Chronique',
                'route' => 'manage.chronicles.show',
                'name' => fn (Chronicle $item) => $item->title,
                'world' => fn (Chronicle $item) => optional($item->world)->name,
            ],
            [
                'class' => Place::class,
                'label' => 'Lieu',
                'route' => 'manage.places.show',
                'name' => fn (Place $item) => $item->name,
                'world' => fn (Place $item) => optional($item->world)->name,
            ],
            [
                'class' => World::class,
                'label' => 'Monde',
                'route' => 'manage.worlds.show',
                'name' => fn (World $item) => $item->name,
                'world' => fn (World $item) => null,
            ],
            [
                'class' => Faction::class,
                'label' => 'Faction',
                'route' => 'manage.factions.show',
                'name' => fn (Faction $item) => $item->name,
                'world' => fn (Faction $item) => optional($item->world)->name,
            ],
        ];

        foreach ($typeConfigs as $config) {
            $query = $config['class']::withTrashed()
                ->with('world')
                ->where(function ($builder) use ($since) {
                    $builder->where('created_at', '>=', $since)
                        ->orWhere('updated_at', '>=', $since)
                        ->orWhere('deleted_at', '>=', $since);
                })
                ->latest('updated_at');

            foreach ($query->take(5)->get() as $item) {
                $at = $item->deleted_at ?? ($item->updated_at ?? $item->created_at);
                if (!$at || $at->lt($since)) {
                    continue;
                }

                $action = $item->deleted_at && $item->deleted_at->gte($since)
                    ? 'Supprimé'
                    : ($item->updated_at && $item->updated_at->gt($item->created_at) && $item->updated_at->gte($since)
                        ? 'Modifié'
                        : 'Créé');

                $activities->push([
                    'action' => $action,
                    'type' => $config['label'],
                    'label' => $config['name']($item),
                    'meta' => trim(implode(' · ', array_filter([
                        $config['world']($item),
                    ]))),
                    'url' => Route::has($config['route']) ? route($config['route'], $item) : null,
                    'at' => $at,
                ]);
            }
        }

        return $activities
            ->sortByDesc('at')
            ->take(10)
            ->values();
    }

    private function buildContinueItem(Request $request): ?array
    {
        $lastOpen = $request->session()->get('manage_last_open');
        if (is_array($lastOpen) && !empty($lastOpen['url']) && !empty($lastOpen['label'])) {
            return $lastOpen;
        }

        return null;
    }

    private function buildFavoritePages(?int $userId): Collection
    {
        $shortcuts = collect([
            ['route_name' => 'manage.characters.index', 'label' => 'Personnages', 'icon' => '✦'],
            ['route_name' => 'manage.chronicles.index', 'label' => 'Chroniques', 'icon' => '✧'],
            ['route_name' => 'manage.places.index', 'label' => 'Lieux', 'icon' => '⌂'],
            ['route_name' => 'manage.worlds.index', 'label' => 'Mondes', 'icon' => '◌'],
            ['route_name' => 'manage.genealogy.index', 'label' => 'Généalogie', 'icon' => '⋆'],
            ['route_name' => 'manage.gallery.index', 'label' => 'Galerie', 'icon' => '◴'],
            ['route_name' => 'manage.factions.index', 'label' => 'Factions', 'icon' => '⬢'],
            ['route_name' => 'manage.analytics.index', 'label' => 'Analytics', 'icon' => '◎'],
        ])->filter(fn (array $shortcut) => Route::has($shortcut['route_name']));

        if (!$userId || $shortcuts->isEmpty()) {
            return $shortcuts->map(function (array $shortcut) {
                return [
                    'label' => $shortcut['label'],
                    'icon' => $shortcut['icon'],
                    'url' => route($shortcut['route_name']),
                    'count' => 0,
                ];
            })->take(4)->values();
        }

        $since = now()->subDays(30);
        $counts = UserLog::query()
            ->where('user_id', $userId)
            ->where('action_user', 'page_consulte')
            ->where('action_at', '>=', $since)
            ->whereIn('route_name', $shortcuts->pluck('route_name')->all())
            ->select('route_name', DB::raw('COUNT(*) as visits'))
            ->groupBy('route_name')
            ->pluck('visits', 'route_name');

        return $shortcuts
            ->map(function (array $shortcut) use ($counts) {
                return [
                    'label' => $shortcut['label'],
                    'icon' => $shortcut['icon'],
                    'url' => route($shortcut['route_name']),
                    'count' => (int) ($counts[$shortcut['route_name']] ?? 0),
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();
    }

    private function buildIncompleteItems(): Collection
    {
        $items = collect();

        $characters = Character::with('world')
            ->where(function ($query) {
                $query->whereNull('summary')
                    ->orWhere('summary', '')
                    ->orWhereNull('image_path')
                    ->orWhereNull('birth_date')
                    ->orWhereNull('role');
            })
            ->latest('updated_at')
            ->take(5)
            ->get();

        foreach ($characters as $character) {
            $missing = [];
            if (trim((string) $character->summary) === '') {
                $missing[] = 'Résumé';
            }
            if (!$character->image_path) {
                $missing[] = 'Image';
            }
            if (!$character->birth_date) {
                $missing[] = 'Naissance';
            }
            if (!$character->role) {
                $missing[] = 'Rôle';
            }

            $items->push([
                'type' => 'Personnage',
                'label' => $character->display_name,
                'url' => route('manage.characters.show', $character),
                'missing' => $missing,
            ]);
        }

        $places = Place::with('world')
            ->where(function ($query) {
                $query->whereNull('summary')
                    ->orWhere('summary', '')
                    ->orWhereNull('image_path');
            })
            ->latest('updated_at')
            ->take(3)
            ->get();

        foreach ($places as $place) {
            $missing = [];
            if (trim((string) $place->summary) === '') {
                $missing[] = 'Résumé';
            }
            if (!$place->image_path) {
                $missing[] = 'Image';
            }

            $items->push([
                'type' => 'Lieu',
                'label' => $place->name,
                'url' => route('manage.places.show', $place),
                'missing' => $missing,
            ]);
        }

        $chronicles = Chronicle::with('world')
            ->where(function ($query) {
                $query->whereNull('summary')
                    ->orWhere('summary', '')
                    ->orWhereNull('content');
            })
            ->latest('updated_at')
            ->take(3)
            ->get();

        foreach ($chronicles as $chronicle) {
            $missing = [];
            if (trim((string) $chronicle->summary) === '') {
                $missing[] = 'Résumé';
            }
            if (trim((string) $chronicle->content) === '') {
                $missing[] = 'Contenu';
            }

            $items->push([
                'type' => 'Chronique',
                'label' => $chronicle->title,
                'url' => route('manage.chronicles.show', $chronicle),
                'missing' => $missing,
            ]);
        }

        $world = $this->currentWorld();
        if ($world) {
            $missing = [];
            if (trim((string) $world->summary) === '') {
                $missing[] = 'Résumé';
            }

            if (!empty($missing)) {
                $items->push([
                    'type' => 'Monde',
                    'label' => $world->name,
                    'url' => route('manage.worlds.show', $world),
                    'missing' => $missing,
                ]);
            }
        }

        return $items->take(6)->values();
    }

    private function buildWorldProgress(): array
    {
        $world = $this->currentWorld();
        if (!$world) {
            return [
                'overall' => 0,
                'items' => [],
            ];
        }

        $characters = Character::count();
        $places = Place::count();
        $chronicles = Chronicle::count();
        $factions = Faction::count();

        $characterComplete = $characters > 0
            ? Character::whereNotNull('summary')
                ->where('summary', '!=', '')
                ->whereNotNull('image_path')
                ->whereNotNull('birth_date')
                ->count()
            : 0;

        $placeComplete = $places > 0
            ? Place::whereNotNull('summary')
                ->where('summary', '!=', '')
                ->whereNotNull('image_path')
                ->count()
            : 0;

        $chronicleComplete = $chronicles > 0
            ? Chronicle::whereNotNull('summary')
                ->where('summary', '!=', '')
                ->whereNotNull('content')
                ->count()
            : 0;

        $factionComplete = $factions > 0
            ? Faction::whereNotNull('summary')
                ->where('summary', '!=', '')
                ->whereNotNull('leader_id')
                ->count()
            : 0;

        $items = [
            [
                'label' => 'Personnages',
                'value' => $characters > 0 ? (int) round(($characterComplete / $characters) * 100) : 0,
            ],
            [
                'label' => 'Lieux',
                'value' => $places > 0 ? (int) round(($placeComplete / $places) * 100) : 0,
            ],
            [
                'label' => 'Chroniques',
                'value' => $chronicles > 0 ? (int) round(($chronicleComplete / $chronicles) * 100) : 0,
            ],
            [
                'label' => 'Factions',
                'value' => $factions > 0 ? (int) round(($factionComplete / $factions) * 100) : 0,
            ],
        ];

        $overall = (int) round(collect($items)->avg('value') ?? 0);

        return [
            'overall' => $overall,
            'items' => $items,
        ];
    }
}
