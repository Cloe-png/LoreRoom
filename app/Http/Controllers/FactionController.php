<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Faction;
use App\Models\FactionRelation;
use App\Models\Diploma;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FactionController extends Controller
{
    private const FACTION_TYPES = [
        'armée',
        'guilde',
        'clan',
        'organisation secrète',
        'gouvernement',
        'entreprise',
        'école',
    ];

    private const FACTION_STATUSES = [
        'active',
        'dissoute',
        'secrète',
        'détruite',
    ];

    private const MEMBER_STATUSES = [
        'actif',
        'ancien membre',
        'mort',
    ];
    public function index()
    {
        $factions = Faction::with('world')
            ->withCount('members')
            ->withCount('outgoingRelations')
            ->withCount('incomingRelations')
            ->latest()
            ->paginate(10);

        return view('manage.factions.index', compact('factions'));
    }

    public function create()
    {
        $defaultWorld = $this->currentWorld();
        $characters = Character::orderBy('name')->get(['id', 'name', 'first_name', 'last_name']);
        $otherFactions = Faction::orderBy('name')->get(['id', 'name']);

        $typeOptions = self::FACTION_TYPES;
        $statusOptions = self::FACTION_STATUSES;
        $memberStatusOptions = self::MEMBER_STATUSES;

        return view('manage.factions.create', compact(
            'defaultWorld',
            'characters',
            'otherFactions',
            'typeOptions',
            'statusOptions',
            'memberStatusOptions'
        ));
    }

    public function store(Request $request)
    {
        $worldId = $this->requireCurrentWorldId();

        $data = $this->validateFaction($request, $worldId);
        $data['world_id'] = $worldId;

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('factions', 'public');
        }

        $membersRows = $data['members'] ?? [];
        $relationsRows = $data['relations'] ?? [];
        $diplomaRows = $data['diplomas'] ?? [];
        unset($data['members'], $data['relations']);
        unset($data['diplomas']);

        $faction = Faction::create($data);

        $this->syncMembers($faction, $membersRows);
        $this->syncRelations($faction, $relationsRows);
        $this->syncDiplomas($faction, $diplomaRows);

        return redirect()->route('manage.factions.index')->with('success', 'Faction créée.');
    }

    public function show(Faction $faction)
    {
        $this->abortIfOutsideCurrentWorld((int) $faction->world_id);

        $faction->load([
            'world',
            'members',
            'diplomas',
            'leader',
            'coLeader',
            'founder',
            'outgoingRelations.relatedFaction',
            'incomingRelations.faction',
        ]);

        return view('manage.factions.show', [
            'faction' => $faction,
            'membersCount' => $faction->members()->count(),
            'relationsCount' => $faction->outgoingRelations()->count() + $faction->incomingRelations()->count(),
        ]);
    }

    public function edit(Faction $faction)
    {
        $this->abortIfOutsideCurrentWorld((int) $faction->world_id);

        $defaultWorld = $this->currentWorld();
        $characters = Character::orderBy('name')->get(['id', 'name', 'first_name', 'last_name']);
        $otherFactions = Faction::where('id', '!=', $faction->id)->orderBy('name')->get(['id', 'name']);
        $typeOptions = self::FACTION_TYPES;
        $statusOptions = self::FACTION_STATUSES;
        $memberStatusOptions = self::MEMBER_STATUSES;

        $memberRows = $faction->members->map(function (Character $character) {
            return [
                'character_id' => $character->id,
                'role' => $character->pivot->role,
                'grade' => $character->pivot->grade,
                'joined_at' => $character->pivot->joined_at,
                'status' => $character->pivot->status,
            ];
        })->values();

        $relationRows = $faction->outgoingRelations()->get()->map(function (FactionRelation $relation) {
            return [
                'related_faction_id' => $relation->related_faction_id,
                'relation_type' => $relation->relation_type,
                'description' => $relation->description,
                'is_bidirectional' => (bool) $relation->is_bidirectional,
            ];
        })->values();

        $diplomaRows = $faction->diplomas()->get()->map(function (Diploma $diploma) {
            return [
                'name' => $diploma->name,
                'level' => $diploma->level,
                'description' => $diploma->description,
            ];
        })->values();

        return view('manage.factions.edit', compact(
            'faction',
            'defaultWorld',
            'characters',
            'otherFactions',
            'typeOptions',
            'statusOptions',
            'memberStatusOptions',
            'memberRows',
            'relationRows',
            'diplomaRows'
        ));
    }

    public function update(Request $request, Faction $faction)
    {
        $this->abortIfOutsideCurrentWorld((int) $faction->world_id);
        $worldId = $this->requireCurrentWorldId();

        $data = $this->validateFaction($request, $worldId);
        $data['world_id'] = $worldId;

        if ($request->hasFile('logo')) {
            if ($faction->logo_path) {
                \Storage::disk('public')->delete($faction->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('factions', 'public');
        }

        $membersRows = $data['members'] ?? [];
        $relationsRows = $data['relations'] ?? [];
        $diplomaRows = $data['diplomas'] ?? [];
        unset($data['members'], $data['relations']);
        unset($data['diplomas']);

        $faction->update($data);

        $this->syncMembers($faction, $membersRows);
        $this->syncRelations($faction, $relationsRows);
        $this->syncDiplomas($faction, $diplomaRows);

        return redirect()->route('manage.factions.index')->with('success', 'Faction mise à jour.');
    }

    public function destroy(Faction $faction)
    {
        $this->abortIfOutsideCurrentWorld((int) $faction->world_id);

        if ($faction->logo_path) {
            \Storage::disk('public')->delete($faction->logo_path);
        }

        $faction->delete();

        return redirect()->route('manage.factions.index')->with('success', 'Faction supprimée.');
    }

    private function validateFaction(Request $request, int $worldId): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['nullable', Rule::in(self::FACTION_TYPES)],
            'summary' => ['nullable', 'string', 'max:3000'],
            'motto' => ['nullable', 'string', 'max:200'],
            'founded_at' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(self::FACTION_STATUSES)],
            'leader_id' => ['nullable', Rule::exists('characters', 'id')->where('world_id', $worldId)],
            'co_leader_id' => ['nullable', Rule::exists('characters', 'id')->where('world_id', $worldId)],
            'founder_id' => ['nullable', Rule::exists('characters', 'id')->where('world_id', $worldId)],
            'logo' => ['nullable', 'image', 'max:4096'],
            'members' => ['nullable', 'array'],
            'members.*.character_id' => ['nullable', Rule::exists('characters', 'id')->where('world_id', $worldId)],
            'members.*.role' => ['nullable', 'string', 'max:120'],
            'members.*.grade' => ['nullable', 'string', 'max:120'],
            'members.*.joined_at' => ['nullable', 'date'],
            'members.*.status' => ['nullable', Rule::in(self::MEMBER_STATUSES)],
            'relations' => ['nullable', 'array'],
            'relations.*.related_faction_id' => ['nullable', Rule::exists('factions', 'id')->where('world_id', $worldId)],
            'relations.*.relation_type' => ['nullable', 'string', 'max:60'],
            'relations.*.description' => ['nullable', 'string', 'max:1000'],
            'relations.*.is_bidirectional' => ['nullable', 'boolean'],
            'diplomas' => ['nullable', 'array'],
            'diplomas.*.name' => ['nullable', 'string', 'max:160'],
            'diplomas.*.level' => ['nullable', 'string', 'max:120'],
            'diplomas.*.description' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function syncMembers(Faction $faction, array $rows): void
    {
        $members = [];

        foreach ($rows as $row) {
            $characterId = (int) ($row['character_id'] ?? 0);
            if ($characterId <= 0) {
                continue;
            }

            $role = trim((string) ($row['role'] ?? ''));
            $grade = trim((string) ($row['grade'] ?? ''));
            $status = trim((string) ($row['status'] ?? ''));
            $joinedAt = isset($row['joined_at']) && $row['joined_at'] !== '' ? (string) $row['joined_at'] : null;
            $members[$characterId] = [
                'role' => $role !== '' ? $role : null,
                'grade' => $grade !== '' ? $grade : null,
                'status' => $status !== '' ? $status : null,
                'joined_at' => $joinedAt,
            ];
        }

        $faction->members()->sync($members);
    }

    private function syncRelations(Faction $faction, array $rows): void
    {
        $faction->outgoingRelations()->delete();

        $seen = [];
        foreach ($rows as $row) {
            $relatedId = (int) ($row['related_faction_id'] ?? 0);
            $relationType = trim((string) ($row['relation_type'] ?? ''));

            if ($relatedId <= 0 || $relatedId === (int) $faction->id || $relationType === '') {
                continue;
            }
            if (isset($seen[$relatedId])) {
                continue;
            }
            $seen[$relatedId] = true;

            $faction->outgoingRelations()->create([
                'related_faction_id' => $relatedId,
                'relation_type' => $relationType,
                'description' => trim((string) ($row['description'] ?? '')) ?: null,
                'is_bidirectional' => !empty($row['is_bidirectional']),
            ]);
        }
    }

    private function syncDiplomas(Faction $faction, array $rows): void
    {
        $faction->diplomas()->delete();

        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $faction->diplomas()->create([
                'name' => $name,
                'level' => trim((string) ($row['level'] ?? '')) ?: null,
                'description' => trim((string) ($row['description'] ?? '')) ?: null,
            ]);
        }
    }
}
