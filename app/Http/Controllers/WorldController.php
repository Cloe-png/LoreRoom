<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterEducation;
use App\Models\CharacterEvent;
use App\Models\CharacterGalleryImage;
use App\Models\CharacterItem;
use App\Models\CharacterJob;
use App\Models\CharacterRelation;
use App\Models\Chronicle;
use App\Models\Diploma;
use App\Models\Faction;
use App\Models\FactionMembership;
use App\Models\FactionRelation;
use App\Models\Job;
use App\Models\LoreEntry;
use App\Models\Place;
use App\Models\PlaceGalleryImage;
use App\Models\Species;
use App\Models\World;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorldController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $worlds = $user
            ->worlds()
            ->latest('created_at')
            ->paginate(10);

        return view('manage.worlds.index', [
            'worlds' => $worlds,
            'activeWorldId' => (int) ($user->current_world_id ?? 0),
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        return view('manage.worlds.create', [
            'importableWorlds' => $user->worlds()->latest('created_at')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'source_world_id' => ['nullable', 'integer'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['status'] = 'active';
        $data['summary'] = trim((string) ($data['summary'] ?? '')) ?: null;

        $user = Auth::user();
        $data['user_id'] = (int) $user->id;
        $sourceWorldId = (int) ($data['source_world_id'] ?? 0);
        unset($data['source_world_id']);

        $world = DB::transaction(function () use ($data, $sourceWorldId, $user) {
            $world = World::create($data);

            if ($sourceWorldId > 0) {
                $sourceWorld = $user->worlds()->whereKey($sourceWorldId)->first();
                if (!$sourceWorld) {
                    abort(422, 'Le monde source sélectionné est introuvable.');
                }

                $this->duplicateWorldContent($sourceWorld, $world);
            }

            return $world;
        });

        if (!(int) ($user->current_world_id ?? 0)) {
            $user->forceFill(['current_world_id' => (int) $world->id])->save();
            $request->session()->put('selected_world_id', (int) $world->id);
        }

        return redirect()->route('manage.worlds.index')->with(
            'success',
            $sourceWorldId > 0 ? 'Monde importé avec succès.' : 'Monde créé.'
        );
    }

    public function switch(Request $request, World $world)
    {
        $user = Auth::user();
        if ((int) $world->user_id !== (int) $user->id) {
            abort(404);
        }

        $user->forceFill(['current_world_id' => (int) $world->id])->save();
        $request->session()->put('selected_world_id', (int) $world->id);

        return redirect()->route('manage.index')->with('success', 'Monde actif changé.');
    }

    public function show(World $world)
    {
        if ((int) $world->user_id !== (int) Auth::id()) {
            abort(404);
        }

        $world->load(['characters', 'places', 'chronicles']);

        return view('manage.worlds.show', compact('world'));
    }

    public function edit(World $world)
    {
        if ((int) $world->user_id !== (int) Auth::id()) {
            abort(404);
        }

        return view('manage.worlds.edit', compact('world'));
    }

    public function update(Request $request, World $world)
    {
        if ((int) $world->user_id !== (int) Auth::id()) {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['name'], $world->id);
        $data['status'] = $world->status ?: 'active';
        $data['summary'] = trim((string) ($data['summary'] ?? '')) ?: null;

        $world->update($data);

        return redirect()->route('manage.worlds.index')->with('success', 'Monde mis à jour.');
    }

    public function destroy(World $world)
    {
        $user = Auth::user();
        if ((int) $world->user_id !== (int) $user->id) {
            abort(404);
        }

        $nextWorldId = (int) ($user->worlds()->where('id', '!=', $world->id)->orderBy('id')->value('id') ?? 0);

        $world->delete();

        $user->refresh();
        $user->forceFill(['current_world_id' => $nextWorldId > 0 ? $nextWorldId : null])->save();
        if ($nextWorldId > 0) {
            request()->session()->put('selected_world_id', $nextWorldId);
        } else {
            request()->session()->forget('selected_world_id');
        }

        return redirect()->route('manage.worlds.index')->with('success', 'Monde supprimé.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $prefix = $base !== '' ? $base : 'monde';
        $slug = $prefix;
        $i = 2;

        while (
            World::query()
                ->when($ignoreId, function ($q) use ($ignoreId) {
                    $q->where('id', '!=', $ignoreId);
                })
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $prefix . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function duplicateWorldContent(World $sourceWorld, World $targetWorld): void
    {
        $placeMap = [];
        $speciesMap = [];
        $jobMap = [];
        $factionMap = [];
        $diplomaMap = [];
        $characterMap = [];
        $chronicleMap = [];

        LoreEntry::withoutGlobalScopes()
            ->where('world_id', $sourceWorld->id)
            ->get()
            ->each(function (LoreEntry $entry) use ($targetWorld) {
                $clone = $entry->replicate();
                $clone->world_id = $targetWorld->id;
                $clone->save();
            });

        Species::withoutGlobalScopes()
            ->where('world_id', $sourceWorld->id)
            ->get()
            ->each(function (Species $species) use ($targetWorld, &$speciesMap) {
                $clone = $species->replicate();
                $clone->world_id = $targetWorld->id;
                $clone->save();
                $speciesMap[(int) $species->id] = (int) $clone->id;
            });

        Job::query()
            ->where('world_id', $sourceWorld->id)
            ->get()
            ->each(function (Job $job) use ($targetWorld, &$jobMap) {
                $clone = $job->replicate();
                $clone->world_id = $targetWorld->id;
                $clone->save();
                $jobMap[(int) $job->id] = (int) $clone->id;
            });

        Place::withoutGlobalScopes()
            ->where('world_id', $sourceWorld->id)
            ->get()
            ->each(function (Place $place) use ($targetWorld, &$placeMap) {
                $clone = $place->replicate();
                $clone->world_id = $targetWorld->id;
                $clone->save();
                $placeMap[(int) $place->id] = (int) $clone->id;
            });

        PlaceGalleryImage::query()
            ->whereIn('place_id', array_keys($placeMap))
            ->get()
            ->each(function (PlaceGalleryImage $image) use (&$placeMap) {
                $clone = $image->replicate();
                $clone->place_id = $placeMap[(int) $image->place_id] ?? null;
                if ($clone->place_id) {
                    $clone->save();
                }
            });

        Faction::withoutGlobalScopes()
            ->where('world_id', $sourceWorld->id)
            ->get()
            ->each(function (Faction $faction) use ($targetWorld, &$factionMap) {
                $clone = $faction->replicate();
                $clone->world_id = $targetWorld->id;
                $clone->leader_id = null;
                $clone->co_leader_id = null;
                $clone->founder_id = null;
                $clone->save();
                $factionMap[(int) $faction->id] = (int) $clone->id;
            });

        Diploma::query()
            ->whereIn('faction_id', array_keys($factionMap))
            ->get()
            ->each(function (Diploma $diploma) use (&$factionMap, &$diplomaMap) {
                $clone = $diploma->replicate();
                $clone->faction_id = $factionMap[(int) $diploma->faction_id] ?? null;
                if ($clone->faction_id) {
                    $clone->save();
                    $diplomaMap[(int) $diploma->id] = (int) $clone->id;
                }
            });

        Character::withoutGlobalScopes()
            ->where('world_id', $sourceWorld->id)
            ->get()
            ->each(function (Character $character) use ($targetWorld, &$placeMap, &$characterMap) {
                $clone = $character->replicate();
                $clone->world_id = $targetWorld->id;
                $clone->father_id = null;
                $clone->mother_id = null;
                $clone->spouse_id = null;
                $clone->birth_place_id = $placeMap[(int) $character->birth_place_id] ?? null;
                $clone->residence_place_id = $placeMap[(int) $character->residence_place_id] ?? null;
                $clone->save();
                $characterMap[(int) $character->id] = (int) $clone->id;
            });

        Character::withoutGlobalScopes()
            ->where('world_id', $sourceWorld->id)
            ->get()
            ->each(function (Character $character) use (&$characterMap) {
                $clone = Character::withoutGlobalScopes()->find($characterMap[(int) $character->id] ?? 0);
                if (!$clone) {
                    return;
                }

                $clone->forceFill([
                    'father_id' => $characterMap[(int) $character->father_id] ?? null,
                    'mother_id' => $characterMap[(int) $character->mother_id] ?? null,
                    'spouse_id' => $characterMap[(int) $character->spouse_id] ?? null,
                ])->save();
            });

        CharacterGalleryImage::query()
            ->whereIn('character_id', array_keys($characterMap))
            ->get()
            ->each(function (CharacterGalleryImage $image) use (&$characterMap) {
                $clone = $image->replicate();
                $clone->character_id = $characterMap[(int) $image->character_id] ?? null;
                if ($clone->character_id) {
                    $clone->save();
                }
            });

        CharacterItem::query()
            ->whereIn('character_id', array_keys($characterMap))
            ->get()
            ->each(function (CharacterItem $item) use (&$characterMap) {
                $clone = $item->replicate();
                $clone->character_id = $characterMap[(int) $item->character_id] ?? null;
                if ($clone->character_id) {
                    $clone->save();
                }
            });

        CharacterEvent::query()
            ->whereIn('character_id', array_keys($characterMap))
            ->get()
            ->each(function (CharacterEvent $event) use (&$characterMap) {
                $clone = $event->replicate();
                $clone->character_id = $characterMap[(int) $event->character_id] ?? null;
                if ($clone->character_id) {
                    $clone->save();
                }
            });

        CharacterJob::query()
            ->whereIn('character_id', array_keys($characterMap))
            ->get()
            ->each(function (CharacterJob $job) use (&$characterMap, &$jobMap) {
                $clone = $job->replicate();
                $clone->character_id = $characterMap[(int) $job->character_id] ?? null;
                $clone->job_id = $jobMap[(int) $job->job_id] ?? null;
                if ($clone->character_id) {
                    $clone->save();
                }
            });

        CharacterEducation::query()
            ->whereIn('character_id', array_keys($characterMap))
            ->get()
            ->each(function (CharacterEducation $education) use (&$characterMap, &$factionMap, &$diplomaMap) {
                $clone = $education->replicate();
                $clone->character_id = $characterMap[(int) $education->character_id] ?? null;
                $clone->faction_id = $factionMap[(int) $education->faction_id] ?? null;
                $clone->diploma_id = $diplomaMap[(int) $education->diploma_id] ?? null;
                if ($clone->character_id) {
                    $clone->save();
                }
            });

        DB::table('character_species')
            ->whereIn('character_id', array_keys($characterMap))
            ->get()
            ->each(function ($row) use (&$characterMap, &$speciesMap) {
                $characterId = $characterMap[(int) $row->character_id] ?? null;
                $speciesId = $speciesMap[(int) $row->species_id] ?? null;
                if ($characterId && $speciesId) {
                    DB::table('character_species')->insert([
                        'character_id' => $characterId,
                        'species_id' => $speciesId,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }
            });

        DB::table('character_exes')
            ->whereIn('character_id', array_keys($characterMap))
            ->get()
            ->each(function ($row) use (&$characterMap) {
                $characterId = $characterMap[(int) $row->character_id] ?? null;
                $exCharacterId = $characterMap[(int) $row->ex_character_id] ?? null;
                if ($characterId && $exCharacterId) {
                    $exists = DB::table('character_exes')
                        ->where('character_id', $characterId)
                        ->where('ex_character_id', $exCharacterId)
                        ->exists();

                    if (!$exists) {
                        DB::table('character_exes')->insert([
                            'character_id' => $characterId,
                            'ex_character_id' => $exCharacterId,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ]);
                    }
                }
            });

        CharacterRelation::query()
            ->whereIn('from_character_id', array_keys($characterMap))
            ->whereIn('to_character_id', array_keys($characterMap))
            ->get()
            ->each(function (CharacterRelation $relation) use (&$characterMap) {
                $clone = $relation->replicate();
                $clone->from_character_id = $characterMap[(int) $relation->from_character_id] ?? null;
                $clone->to_character_id = $characterMap[(int) $relation->to_character_id] ?? null;
                if ($clone->from_character_id && $clone->to_character_id) {
                    $clone->save();
                }
            });

        Faction::withoutGlobalScopes()
            ->where('world_id', $sourceWorld->id)
            ->get()
            ->each(function (Faction $faction) use (&$factionMap, &$characterMap) {
                $clone = Faction::withoutGlobalScopes()->find($factionMap[(int) $faction->id] ?? 0);
                if (!$clone) {
                    return;
                }

                $clone->forceFill([
                    'leader_id' => $characterMap[(int) $faction->leader_id] ?? null,
                    'co_leader_id' => $characterMap[(int) $faction->co_leader_id] ?? null,
                    'founder_id' => $characterMap[(int) $faction->founder_id] ?? null,
                ])->save();
            });

        FactionMembership::query()
            ->whereIn('faction_id', array_keys($factionMap))
            ->get()
            ->each(function (FactionMembership $membership) use (&$factionMap, &$characterMap) {
                $clone = $membership->replicate();
                $clone->faction_id = $factionMap[(int) $membership->faction_id] ?? null;
                $clone->character_id = $characterMap[(int) $membership->character_id] ?? null;
                if ($clone->faction_id && $clone->character_id) {
                    $clone->save();
                }
            });

        FactionRelation::query()
            ->whereIn('faction_id', array_keys($factionMap))
            ->whereIn('related_faction_id', array_keys($factionMap))
            ->get()
            ->each(function (FactionRelation $relation) use (&$factionMap) {
                $clone = $relation->replicate();
                $clone->faction_id = $factionMap[(int) $relation->faction_id] ?? null;
                $clone->related_faction_id = $factionMap[(int) $relation->related_faction_id] ?? null;
                if ($clone->faction_id && $clone->related_faction_id) {
                    $clone->save();
                }
            });

        Chronicle::withoutGlobalScopes()
            ->where('world_id', $sourceWorld->id)
            ->get()
            ->each(function (Chronicle $chronicle) use ($targetWorld, &$placeMap, &$chronicleMap) {
                $clone = $chronicle->replicate();
                $clone->world_id = $targetWorld->id;
                $clone->event_place_id = $placeMap[(int) $chronicle->event_place_id] ?? null;
                $clone->save();
                $chronicleMap[(int) $chronicle->id] = (int) $clone->id;
            });

        DB::table('chronicle_character')
            ->whereIn('chronicle_id', array_keys($chronicleMap))
            ->get()
            ->each(function ($row) use (&$chronicleMap, &$characterMap) {
                $chronicleId = $chronicleMap[(int) $row->chronicle_id] ?? null;
                $characterId = $characterMap[(int) $row->character_id] ?? null;
                if ($chronicleId && $characterId) {
                    DB::table('chronicle_character')->insert([
                        'chronicle_id' => $chronicleId,
                        'character_id' => $characterId,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }
            });
    }
}
