<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterGalleryImage;
use App\Models\CharacterRelation;
use App\Models\Chronicle;
use App\Models\Faction;
use App\Models\Job;
use App\Models\LoreEntry;
use App\Models\Place;
use App\Models\PlaceGalleryImage;
use App\Models\Species;
use App\Models\WritingPrompt;
use App\Support\TodayBirthdays;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardHomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $world = $this->currentWorld();
        $today = Carbon::today(config('app.timezone'));
        $steps = null;
        if (!$user->onboarding_completed_at) {
            $characterIds = $world ? Character::where('world_id', $world->id)->pluck('id') : collect();
            $steps = [
                ['label' => 'Créer un monde', 'done' => $user->worlds()->exists(), 'url' => route('manage.worlds.create')],
                ['label' => 'Créer un personnage', 'done' => $world && Character::where('world_id', $world->id)->exists(), 'url' => route('manage.characters.create')],
                ['label' => 'Créer un lieu', 'done' => $world && Place::where('world_id', $world->id)->exists(), 'url' => route('manage.places.create')],
                ['label' => 'Relier deux personnages', 'done' => $characterIds->count() > 1 && CharacterRelation::whereIn('from_character_id', $characterIds)->whereIn('to_character_id', $characterIds)->exists(), 'url' => route('manage.relations.index')],
                ['label' => 'Ajouter un événement', 'done' => $world && Chronicle::where('world_id', $world->id)->exists(), 'url' => route('manage.chronicles.create')],
            ];
            if (collect($steps)->every(fn ($step) => $step['done'])) {
                $user->forceFill(['onboarding_completed_at' => now()])->save();
            }
        }

        $idea = $world ? $this->ideaOfDay($request, $world->id, $user->id) : null;
        $incomplete = $world ? $this->incomplete($world->id, $user, $request->boolean('incomplete')) : collect();
        $timeline = $world ? $this->timeline($world->id) : collect();
        $latestNews = DB::table('changelog_entries')->where('is_published', true)->whereNotNull('published_at')->orderByDesc('published_at')->first();
        $worldCounts = $world ? Cache::remember("dashboard:world-counts:{$user->id}:{$world->id}", 60, function () use ($world, $user) {
            $counts = [
                ['label' => 'Personnages', 'route' => 'manage.characters.index', 'count' => Character::where('world_id', $world->id)->count()],
                ['label' => 'Lieux', 'route' => 'manage.places.index', 'count' => Place::where('world_id', $world->id)->count()],
                ['label' => 'Factions', 'route' => 'manage.factions.index', 'module' => 'factions', 'count' => Faction::where('world_id', $world->id)->count()],
                ['label' => 'Espèces', 'route' => 'manage.species.index', 'module' => 'lore', 'count' => Species::where('world_id', $world->id)->count()],
                ['label' => 'Métiers', 'route' => 'manage.jobs.index', 'module' => 'jobs', 'count' => Job::where('world_id', $world->id)->count()],
                ['label' => 'Lore', 'route' => 'manage.lore.index', 'module' => 'lore', 'count' => LoreEntry::where('world_id', $world->id)->count()],
                ['label' => 'Événements', 'route' => 'manage.chronicles.index', 'count' => Chronicle::where('world_id', $world->id)->count()],
                ['label' => 'Galerie', 'route' => 'manage.gallery.index', 'module' => 'gallery', 'count' => CharacterGalleryImage::whereHas('character', fn ($query) => $query->where('world_id', $world->id))->count() + PlaceGalleryImage::whereHas('place', fn ($query) => $query->where('world_id', $world->id))->count()],
            ];
            return collect($counts)->filter(fn ($item) => empty($item['module']) || $user->canAccessModule($item['module']))->values();
        }) : collect();

        return view('manage.home', [
            'activeWorld' => $world,
            'todayBirthdays' => app(TodayBirthdays::class)->forWorld($world, $user, $today),
            'today' => $today,
            'steps' => $steps,
            'accomplishedAt' => $user->onboarding_completed_at,
            'idea' => $idea,
            'incompleteItems' => $incomplete,
            'timeline' => $timeline,
            'timelineFallback' => (bool) $world,
            'latestNews' => $latestNews,
            'newsIsNew' => $latestNews && $latestNews->id > (int) ($user->changelog_seen_id ?? 0) && Carbon::parse($latestNews->published_at)->gte(now()->subDays(7)),
            'worldCounts' => $worldCounts,
        ]);
    }

    private function ideaOfDay(Request $request, int $worldId, int $userId): ?array
    {
        $date = now(config('app.timezone'))->toDateString();
        $identity = ['user_id' => $userId, 'world_id' => $worldId, 'requested_on' => $date];
        DB::table('writing_prompt_rotations')->insertOrIgnore($identity + [
            'alternative_count' => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);
        if ($request->routeIs('manage.prompts.another')) {
            DB::table('writing_prompt_rotations')->where($identity)->where('alternative_count', '<', 3)->update([
                'alternative_count' => DB::raw('alternative_count + 1'), 'prompt_id' => null,
                'target_type' => null, 'target_id' => null, 'target_name' => null, 'updated_at' => now(),
            ]);
        }
        $rotation = DB::table('writing_prompt_rotations')->where($identity)->first();
        $alternativeCount = (int) $rotation->alternative_count;
        $prompts = WritingPrompt::where('is_active', true)->orderBy('id')->get();
        if ($prompts->isEmpty()) return null;
        $moduleByType = ['faction' => 'factions', 'species' => 'lore', 'job' => 'jobs', 'lore' => 'lore'];
        $map = [
            'character' => [Character::class, 'manage.characters.edit', 'display_name'], 'place' => [Place::class, 'manage.places.edit', 'name'],
            'faction' => [Faction::class, 'manage.factions.edit', 'name'], 'species' => [Species::class, 'manage.species.edit', 'name'],
            'job' => [Job::class, 'manage.jobs.edit', 'name'], 'event' => [Chronicle::class, 'manage.chronicles.edit', 'title'],
            'lore' => [LoreEntry::class, 'manage.lore.edit', 'title'],
        ];
        $completenessField = ['character' => 'summary', 'place' => 'description', 'faction' => 'summary', 'species' => 'characteristics', 'job' => 'description', 'event' => 'summary', 'lore' => 'content'];
        $createRoute = ['character'=>'manage.characters.create','place'=>'manage.places.create','faction'=>'manage.factions.create','species'=>'manage.species.create','job'=>'manage.jobs.create','event'=>'manage.chronicles.create','lore'=>'manage.lore.create'];
        $createLabel = ['character'=>'Créer un personnage','place'=>'Créer un lieu','faction'=>'Créer une faction','species'=>'Créer une espèce','job'=>'Créer un métier','event'=>'Créer un événement','lore'=>'Créer un article de lore'];

        if ($rotation->prompt_id && $rotation->target_type) {
            $prompt = WritingPrompt::where('is_active', true)->find($rotation->prompt_id);
            $type = $rotation->target_type;
            if ($prompt && isset($map[$type]) && (!isset($moduleByType[$type]) || $request->user()->canAccessModule($moduleByType[$type]))) {
                [$class, $editRoute, $nameField] = $map[$type];
                $item = $rotation->target_id ? $class::where('world_id', $worldId)->find($rotation->target_id) : null;
                if (!$rotation->target_id || $item) {
                    return $this->ideaData($prompt, $type, $item, $rotation->target_name, $editRoute, $createRoute[$type], $createLabel[$type], $alternativeCount);
                }
            }
        }

        $dayOffset = Carbon::parse($date, config('app.timezone'))->diffInDays(Carbon::parse('2000-01-01', config('app.timezone')));
        $seed = crc32($userId . ':' . $worldId) + ($dayOffset * 4) + $alternativeCount;
        $prompt = $prompts[$seed % $prompts->count()];
        $types = $prompt->target_type === 'any' ? ['character','place','faction','species','job','event','lore'] : [$prompt->target_type];
        $types = array_values(array_filter($types, fn ($type) => !isset($moduleByType[$type]) || $request->user()->canAccessModule($moduleByType[$type])));
        if ($types === []) return null;
        foreach ($types as $type) {
            if (!isset($map[$type])) continue;
            [$class, $editRoute, $nameField] = $map[$type];
            $query = $class::query();
            $query->where('world_id', $worldId);
            $field = $completenessField[$type];
            $item = $query->orderByRaw("CASE WHEN {$field} IS NULL OR {$field} = '' THEN 0 ELSE 1 END")
                ->orderBy('updated_at')->first();
            if ($item) {
                DB::table('writing_prompt_rotations')->where($identity)->update(['prompt_id' => $prompt->id, 'target_type' => $type, 'target_id' => $item->id, 'target_name' => (string) $item->{$nameField}, 'updated_at' => now()]);
                return $this->ideaData($prompt, $type, $item, (string) $item->{$nameField}, $editRoute, $createRoute[$type], $createLabel[$type], $alternativeCount);
            }
        }
        $type = $types[0];
        DB::table('writing_prompt_rotations')->where($identity)->update(['prompt_id' => $prompt->id, 'target_type' => $type, 'target_id' => null, 'target_name' => 'ce monde', 'updated_at' => now()]);
        return $this->ideaData($prompt, $type, null, 'ce monde', $map[$type][1], $createRoute[$type], $createLabel[$type], $alternativeCount);
    }

    private function ideaData($prompt, string $type, $item, ?string $name, string $editRoute, string $createRoute, string $createLabel, int $alternativeCount): array
    {
        $url = $item ? route($editRoute, $item) . ($prompt->section_hint ? '#'.$prompt->section_hint : '') : route($createRoute);
        return ['text' => str_replace('{nom}', $name ?? 'ce monde', $prompt->text), 'url' => $url, 'createLabel' => $item ? null : $createLabel, 'another' => $alternativeCount < 3];
    }

    private function incomplete(int $worldId, $user, bool $all = false)
    {
        $items = collect();
        foreach (config('home.incomplete') as $table => $config) {
            $requiredModule = ['factions' => 'factions', 'species' => 'lore', 'jobs' => 'jobs', 'lore_entries' => 'lore'][$table] ?? null;
            if ($requiredModule && !$user->canAccessModule($requiredModule)) continue;
            $class = ['characters'=>Character::class,'places'=>Place::class,'factions'=>Faction::class,'species'=>Species::class,'jobs'=>Job::class,'lore_entries'=>LoreEntry::class][$table];
            $query = $class::where('world_id', $worldId)
                ->where(function ($q) use ($config) {
                    $q->whereNull($config['field']);
                    if ($config['field'] !== 'birth_date') {
                        $q->orWhere($config['field'], '');
                    }
                })
                ->latest('updated_at');
            $records = $all ? $query->get() : $query->take(5)->get();
            foreach ($records as $record) $items->push(['name'=>$record->{$config['name']},'type'=>$config['label'],'missing'=>$config['missing'],'updated'=>$record->updated_at,'url'=>route($config['route'],$record)]);
        }
        $items = $items->sortByDesc('updated')->values();
        return $all ? $items : $items->take(5)->values();
    }

    private function timeline(int $worldId)
    {
        $dated = Chronicle::with(['eventPlace','linkedCharacters'])->where('world_id',$worldId)->whereNotNull('event_date')->orderByDesc('event_date')->take(3)->get();
        if ($dated->isNotEmpty()) return $dated->reverse()->values();
        return Chronicle::with(['eventPlace','linkedCharacters'])->where('world_id',$worldId)->latest('created_at')->take(3)->get()->reverse()->values();
    }
}
