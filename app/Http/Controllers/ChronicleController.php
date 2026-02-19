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
        $characters = Character::query()
            ->select(['id', 'name', 'first_name', 'last_name', 'preferred_color', 'image_path', 'birth_date'])
            ->orderByRaw('birth_date IS NULL, birth_date ASC')
            ->orderBy('name')
            ->get();

        return view('manage.chronicles.index', compact('characters'));
    }

    public function globalTimeline()
    {
        $timelineEvents = $this->buildGlobalTimeline();

        return view('manage.chronicles.global', compact('timelineEvents'));
    }

    public function characterTimeline(Character $character)
    {
        $timelineEvents = $this->buildCharacterTimeline($character->id);

        return view('manage.chronicles.character', compact('character', 'timelineEvents'));
    }

    private function buildGlobalTimeline(): Collection
    {
        $chronicleEvents = Chronicle::query()
            ->with([
                'world:id,name',
                'linkedCharacters:id,name,first_name,last_name,preferred_color,image_path',
            ])
            ->get()
            ->map(function (Chronicle $chronicle) {
                $linked = $chronicle->linkedCharacters;
                $firstLinked = $linked->first();
                $linkedNames = $linked->pluck('display_name')->filter()->values()->all();

                return [
                    'date' => $chronicle->event_date,
                    'title' => $chronicle->title,
                    'description' => $chronicle->summary,
                    'type' => 'chronicle',
                    'source_name' => optional($chronicle->world)->name,
                    'related_person_name' => $firstLinked ? $firstLinked->display_name : null,
                    'related_person_link' => $firstLinked ? route('manage.characters.show', $firstLinked) : null,
                    'related_people' => $linkedNames,
                    'photo_path' => $firstLinked ? $firstLinked->image_path : null,
                    'accent_color' => $this->resolveColor($firstLinked ? $firstLinked->preferred_color : null),
                    'link' => route('manage.chronicles.show', $chronicle),
                    'edit_link' => route('manage.chronicles.edit', $chronicle),
                    'delete_link' => route('manage.chronicles.destroy', $chronicle),
                    'can_manage' => true,
                ];
            });

        $characterEvents = CharacterEvent::query()
            ->with('character:id,name,first_name,last_name,birth_date,death_date,preferred_color,image_path')
            ->get()
            ->map(function (CharacterEvent $event) {
                $character = $event->character;

                return [
                    'date' => $event->event_date,
                    'title' => $event->title,
                    'description' => $event->details,
                    'type' => 'character_event',
                    'source_name' => $character ? $character->display_name : null,
                    'related_person_name' => $character ? $character->display_name : null,
                    'related_person_link' => $character ? route('manage.characters.show', $character) : null,
                    'related_people' => $character ? [$character->display_name] : [],
                    'photo_path' => $character ? $character->image_path : null,
                    'accent_color' => $this->resolveColor($character ? $character->preferred_color : null),
                    'link' => $character ? route('manage.characters.show', $character) : null,
                    'can_manage' => false,
                ];
            });

        $lifeEvents = Character::query()
            ->select(['id', 'name', 'first_name', 'last_name', 'birth_date', 'death_date', 'preferred_color', 'image_path'])
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
                        'related_person_name' => $character->display_name,
                        'related_person_link' => route('manage.characters.show', $character),
                        'related_people' => [$character->display_name],
                        'photo_path' => $character->image_path,
                        'accent_color' => $this->resolveColor($character->preferred_color),
                        'link' => route('manage.characters.show', $character),
                        'can_manage' => false,
                    ];
                }

                if ($character->death_date) {
                    $events[] = [
                        'date' => $character->death_date,
                        'title' => 'Deces de ' . $character->display_name,
                        'description' => null,
                        'type' => 'death',
                        'source_name' => $character->display_name,
                        'related_person_name' => $character->display_name,
                        'related_person_link' => route('manage.characters.show', $character),
                        'related_people' => [$character->display_name],
                        'photo_path' => $character->image_path,
                        'accent_color' => $this->resolveColor($character->preferred_color),
                        'link' => route('manage.characters.show', $character),
                        'can_manage' => false,
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
            ->select(['id', 'name', 'first_name', 'last_name', 'birth_date', 'death_date', 'preferred_color', 'image_path'])
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
                    'related_person_name' => $character->display_name,
                    'related_person_link' => route('manage.characters.show', $character),
                    'related_people' => [$character->display_name],
                    'photo_path' => $character->image_path,
                    'accent_color' => $this->resolveColor($character->preferred_color),
                    'link' => route('manage.characters.show', $character),
                    'can_manage' => false,
                ];
            });

        $chronicleEntries = $character->chronicles()
            ->with('world:id,name')
            ->get()
            ->map(function (Chronicle $chronicle) use ($character) {
                return [
                    'date' => $chronicle->event_date,
                    'title' => $chronicle->title,
                    'description' => $chronicle->summary,
                    'type' => 'chronicle',
                    'source_name' => optional($chronicle->world)->name,
                    'related_person_name' => $character->display_name,
                    'related_person_link' => route('manage.characters.show', $character),
                    'related_people' => [$character->display_name],
                    'photo_path' => $character->image_path,
                    'accent_color' => $this->resolveColor($character->preferred_color),
                    'link' => route('manage.chronicles.show', $chronicle),
                    'edit_link' => route('manage.chronicles.edit', $chronicle),
                    'delete_link' => route('manage.chronicles.destroy', $chronicle),
                    'can_manage' => true,
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
                'related_person_name' => $character->display_name,
                'related_person_link' => route('manage.characters.show', $character),
                'related_people' => [$character->display_name],
                'photo_path' => $character->image_path,
                'accent_color' => $this->resolveColor($character->preferred_color),
                'link' => route('manage.characters.show', $character),
                'can_manage' => false,
            ]);
        }

        if ($character->death_date) {
            $lifeEntries->push([
                'date' => $character->death_date,
                'title' => 'Deces',
                'description' => null,
                'type' => 'death',
                'source_name' => $character->display_name,
                'related_person_name' => $character->display_name,
                'related_person_link' => route('manage.characters.show', $character),
                'related_people' => [$character->display_name],
                'photo_path' => $character->image_path,
                'accent_color' => $this->resolveColor($character->preferred_color),
                'link' => route('manage.characters.show', $character),
                'can_manage' => false,
            ]);
        }

        return $eventEntries
            ->concat($chronicleEntries)
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
        $characters = Character::query()
            ->select(['id', 'name', 'first_name', 'last_name', 'birth_date'])
            ->orderByRaw('birth_date IS NULL, birth_date ASC')
            ->orderBy('name')
            ->get();

        return view('manage.chronicles.create', compact('defaultWorld', 'characters'));
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
            'linked_character_ids' => ['nullable', 'array'],
            'linked_character_ids.*' => ['integer', 'exists:characters,id'],
        ]);
        $data['world_id'] = $defaultWorldId;
        $linkedCharacterIds = array_values(array_unique(array_map('intval', $data['linked_character_ids'] ?? [])));
        unset($data['linked_character_ids']);

        $chronicle = Chronicle::create($data);
        $chronicle->linkedCharacters()->sync($linkedCharacterIds);

        return redirect()->route('manage.chronicles.index')->with('success', 'Chronique creee.');
    }

    public function show(Chronicle $chronicle)
    {
        $chronicle->load('world');

        return view('manage.chronicles.show', compact('chronicle'));
    }

    public function edit(Chronicle $chronicle)
    {
        $chronicle->load('linkedCharacters:id');
        $defaultWorld = World::query()->orderBy('id')->first();
        $characters = Character::query()
            ->select(['id', 'name', 'first_name', 'last_name', 'birth_date'])
            ->orderByRaw('birth_date IS NULL, birth_date ASC')
            ->orderBy('name')
            ->get();

        return view('manage.chronicles.edit', compact('chronicle', 'defaultWorld', 'characters'));
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
            'linked_character_ids' => ['nullable', 'array'],
            'linked_character_ids.*' => ['integer', 'exists:characters,id'],
        ]);
        $data['world_id'] = $defaultWorldId;
        $linkedCharacterIds = array_values(array_unique(array_map('intval', $data['linked_character_ids'] ?? [])));
        unset($data['linked_character_ids']);

        $chronicle->update($data);
        $chronicle->linkedCharacters()->sync($linkedCharacterIds);

        return redirect()->route('manage.chronicles.index')->with('success', 'Chronique mise à jour.');
    }

    public function destroy(Chronicle $chronicle)
    {
        $chronicle->delete();

        return redirect()->route('manage.chronicles.index')->with('success', 'Chronique supprimée.');
    }
}
