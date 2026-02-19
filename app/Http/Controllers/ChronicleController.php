<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterEvent;
use App\Models\Chronicle;
use App\Models\World;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class ChronicleController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'mode' => ['nullable', Rule::in(['global', 'character'])],
            'character_id' => ['nullable', 'integer', 'exists:characters,id'],
        ]);

        $timelineMode = $validated['mode'] ?? null;
        $selectedCharacterId = isset($validated['character_id']) ? (int) $validated['character_id'] : null;

        $characters = Character::query()
            ->select(['id', 'name', 'first_name', 'last_name', 'preferred_color'])
            ->orderBy('name')
            ->get();

        if ($timelineMode === 'character') {
            $timelineEvents = $this->buildCharacterTimeline($selectedCharacterId);
        } elseif ($timelineMode === 'global') {
            $timelineEvents = $this->buildGlobalTimeline();
        } else {
            $timelineEvents = collect();
        }

        $chronicles = Chronicle::with('world')->latest()->paginate(10);

        return view('manage.chronicles.index', compact(
            'chronicles',
            'characters',
            'timelineEvents',
            'timelineMode',
            'selectedCharacterId'
        ));
    }

    public function characterTimeline(Character $character)
    {
        $timelineEvents = $this->buildCharacterTimeline($character->id);

        return view('manage.chronicles.character', compact('character', 'timelineEvents'));
    }

    private function buildGlobalTimeline(): Collection
    {
        $chronicleEvents = Chronicle::query()
            ->with('world:id,name')
            ->get()
            ->map(function (Chronicle $chronicle) {
                return [
                    'date' => $chronicle->event_date,
                    'title' => $chronicle->title,
                    'description' => $chronicle->summary,
                    'type' => 'chronicle',
                    'source_name' => optional($chronicle->world)->name,
                    'link' => route('manage.chronicles.show', $chronicle),
                ];
            });

        $characterEvents = CharacterEvent::query()
            ->with('character:id,name,first_name,last_name,birth_date,death_date')
            ->get()
            ->map(function (CharacterEvent $event) {
                $character = $event->character;

                return [
                    'date' => $event->event_date,
                    'title' => $event->title,
                    'description' => $event->details,
                    'type' => 'character_event',
                    'source_name' => $character ? $character->display_name : null,
                    'accent_color' => $this->resolveColor($character ? $character->preferred_color : null),
                    'link' => $character ? route('manage.characters.show', $character) : null,
                ];
            });

        $lifeEvents = Character::query()
            ->select(['id', 'name', 'first_name', 'last_name', 'birth_date', 'death_date'])
            ->where(function ($query) {
                $query->whereNotNull('birth_date')
                    ->orWhereNotNull('death_date');
            })
            ->get()
            ->flatMap(function (Character $character) {
                $events = [];

                if ($character->birth_date) {
                    $events[] = [
                        'date' => $character->birth_date,
                        'title' => 'Naissance de ' . $character->display_name,
                        'description' => null,
                        'type' => 'birth',
                        'source_name' => $character->display_name,
                        'accent_color' => $this->resolveColor($character->preferred_color),
                        'link' => route('manage.characters.show', $character),
                    ];
                }

                if ($character->death_date) {
                    $events[] = [
                        'date' => $character->death_date,
                        'title' => 'Deces de ' . $character->display_name,
                        'description' => null,
                        'type' => 'death',
                        'source_name' => $character->display_name,
                        'accent_color' => $this->resolveColor($character->preferred_color),
                        'link' => route('manage.characters.show', $character),
                    ];
                }

                return $events;
            });

        return $chronicleEvents
            ->concat($characterEvents)
            ->concat($lifeEvents)
            ->sort(function (array $a, array $b) {
                if ($a['date'] && $b['date']) {
                    return $a['date']->timestamp <=> $b['date']->timestamp;
                }

                if ($a['date']) {
                    return -1;
                }

                if ($b['date']) {
                    return 1;
                }

                return strcmp((string) $a['title'], (string) $b['title']);
            })
            ->values();
    }

    private function buildCharacterTimeline(?int $characterId): Collection
    {
        if (!$characterId) {
            return collect();
        }

        $character = Character::query()
            ->select(['id', 'name', 'first_name', 'last_name', 'birth_date', 'death_date', 'preferred_color'])
            ->find($characterId);

        if (!$character) {
            return collect();
        }

        $eventEntries = $character->events()
            ->get()
            ->map(function (CharacterEvent $event) use ($character) {
                return [
                    'date' => $event->event_date,
                    'title' => $event->title,
                    'description' => $event->details,
                    'type' => 'character_event',
                    'source_name' => $character->display_name,
                    'accent_color' => $this->resolveColor($character->preferred_color),
                    'link' => route('manage.characters.show', $character),
                ];
            });

        $lifeEntries = collect();
        if ($character->birth_date) {
            $lifeEntries->push([
                'date' => $character->birth_date,
                'title' => 'Naissance',
                'description' => null,
                'type' => 'birth',
                'source_name' => $character->display_name,
                'accent_color' => $this->resolveColor($character->preferred_color),
                'link' => route('manage.characters.show', $character),
            ]);
        }

        if ($character->death_date) {
            $lifeEntries->push([
                'date' => $character->death_date,
                'title' => 'Deces',
                'description' => null,
                'type' => 'death',
                'source_name' => $character->display_name,
                'accent_color' => $this->resolveColor($character->preferred_color),
                'link' => route('manage.characters.show', $character),
            ]);
        }

        return $eventEntries
            ->concat($lifeEntries)
            ->sort(function (array $a, array $b) {
                if ($a['date'] && $b['date']) {
                    return $a['date']->timestamp <=> $b['date']->timestamp;
                }

                if ($a['date']) {
                    return -1;
                }

                if ($b['date']) {
                    return 1;
                }

                return strcmp((string) $a['title'], (string) $b['title']);
            })
            ->values();
    }

    private function resolveColor(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $normalized = trim($value);
        if ($normalized === '') {
            return null;
        }

        if ($normalized[0] !== '#') {
            $normalized = '#' . $normalized;
        }

        return preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $normalized)
            ? strtoupper($normalized)
            : null;
    }

    public function create()
    {
        $defaultWorld = World::query()->orderBy('id')->first();

        return view('manage.chronicles.create', compact('defaultWorld'));
    }

    public function store(Request $request)
    {
        $defaultWorldId = World::query()->value('id');
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => 'Créez d’abord un monde.'])->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'event_date' => ['nullable', 'date'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);
        $data['world_id'] = $defaultWorldId;

        Chronicle::create($data);

        return redirect()->route('manage.chronicles.index')->with('success', 'Chronique creee.');
    }

    public function show(Chronicle $chronicle)
    {
        $chronicle->load('world');

        return view('manage.chronicles.show', compact('chronicle'));
    }

    public function edit(Chronicle $chronicle)
    {
        $defaultWorld = World::query()->orderBy('id')->first();

        return view('manage.chronicles.edit', compact('chronicle', 'defaultWorld'));
    }

    public function update(Request $request, Chronicle $chronicle)
    {
        $defaultWorldId = World::query()->value('id');
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => 'Créez d’abord un monde.'])->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'event_date' => ['nullable', 'date'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);
        $data['world_id'] = $defaultWorldId;

        $chronicle->update($data);

        return redirect()->route('manage.chronicles.index')->with('success', 'Chronique mise à jour.');
    }

    public function destroy(Chronicle $chronicle)
    {
        $chronicle->delete();

        return redirect()->route('manage.chronicles.index')->with('success', 'Chronique supprimée.');
    }
}
