<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterRelation;
use Illuminate\Http\Request;

class CharacterRelationController extends Controller
{
    public function index()
    {
        $q = trim(request('q', ''));

        $relations = CharacterRelation::with(['fromCharacter', 'toCharacter'])
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where('relation_type', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('fromCharacter', function ($sub) use ($like) {
                        $sub->where('name', 'like', $like)
                            ->orWhere('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like);
                    })
                    ->orWhereHas('toCharacter', function ($sub) use ($like) {
                        $sub->where('name', 'like', $like)
                            ->orWhere('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like);
                    });
            })
            ->latest()
            ->paginate(12)
            ->appends(['q' => $q]);

        $relations->getCollection()->transform(function (CharacterRelation $relation) {
            $relation->display_type = $this->resolveDisplayRelationType($relation);
            return $relation;
        });

        $graphRelations = CharacterRelation::with(['fromCharacter', 'toCharacter'])
            ->latest()
            ->take(120)
            ->get()
            ->map(function ($relation) {
                return [
                    'from_id' => $relation->from_character_id,
                    'to_id' => $relation->to_character_id,
                    'from' => optional($relation->fromCharacter)->display_name,
                    'from_status' => optional($relation->fromCharacter)->status,
                    'to' => optional($relation->toCharacter)->display_name,
                    'to_status' => optional($relation->toCharacter)->status,
                    'type' => $this->resolveDisplayRelationType($relation),
                    'bidirectional' => $relation->is_bidirectional,
                ];
            });

        return view('manage.relations.index', compact('relations', 'graphRelations', 'q'));
    }

    public function create()
    {
        $characters = Character::orderBy('name')->get();

        return view('manage.relations.create', compact('characters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_character_id' => ['required', 'exists:characters,id', 'different:to_character_id'],
            'to_character_id' => ['required', 'exists:characters,id'],
            'relation_type' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:3000'],
            'intensity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'is_bidirectional' => ['nullable', 'boolean'],
        ]);

        $data['is_bidirectional'] = $request->boolean('is_bidirectional');
        $data['relation_type'] = $this->normalizeChildTypeBySourceGender($data['relation_type'], (int) $data['from_character_id']);

        CharacterRelation::create($data);

        return redirect()->route('manage.relations.index')->with('success', 'Relation creee.');
    }

    public function show(CharacterRelation $relation)
    {
        $relation->load(['fromCharacter', 'toCharacter']);
        $relation->display_type = $this->resolveDisplayRelationType($relation);

        return view('manage.relations.show', compact('relation'));
    }

    public function edit(CharacterRelation $relation)
    {
        $characters = Character::orderBy('name')->get();

        return view('manage.relations.edit', compact('relation', 'characters'));
    }

    public function update(Request $request, CharacterRelation $relation)
    {
        $data = $request->validate([
            'from_character_id' => ['required', 'exists:characters,id', 'different:to_character_id'],
            'to_character_id' => ['required', 'exists:characters,id'],
            'relation_type' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:3000'],
            'intensity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'is_bidirectional' => ['nullable', 'boolean'],
        ]);

        $data['is_bidirectional'] = $request->boolean('is_bidirectional');
        $data['relation_type'] = $this->normalizeChildTypeBySourceGender($data['relation_type'], (int) $data['from_character_id']);

        $relation->update($data);

        return redirect()->route('manage.relations.index')->with('success', 'Relation mise à jour.');
    }

    public function destroy(CharacterRelation $relation)
    {
        $relation->delete();

        return redirect()->route('manage.relations.index')->with('success', 'Relation supprimée.');
    }

    private function resolveDisplayRelationType(CharacterRelation $relation): string
    {
        $rawType = trim((string) $relation->relation_type);
        $isLegacyFamily = in_array(mb_strtolower($rawType), ['parent de', 'enfant de'], true);
        $description = (string) ($relation->description ?? '');
        $isAutoFamily = strpos($description, '[AUTO_FAMILY]') === 0;

        if (!$isLegacyFamily && !$isAutoFamily) {
            return $rawType !== '' ? $rawType : '-';
        }

        $from = $relation->fromCharacter;
        $to = $relation->toCharacter;
        if (!$from || !$to) {
            return $rawType !== '' ? $rawType : '-';
        }

        if ((int) $to->father_id === (int) $from->id) {
            return 'père';
        }
        if ((int) $to->mother_id === (int) $from->id) {
            return 'mère';
        }
        if ((int) $from->father_id === (int) $to->id || (int) $from->mother_id === (int) $to->id) {
            if ((string) $from->gender === 'femme') {
                return 'fille';
            }
            if ((string) $from->gender === 'homme') {
                return 'fils';
            }
            return 'fils/fille';
        }

        return $rawType !== '' ? $rawType : '-';
    }

    private function normalizeChildTypeBySourceGender(string $relationType, int $fromCharacterId): string
    {
        if (!in_array($relationType, ['fils/fille', 'fils', 'fille'], true)) {
            return $relationType;
        }

        $gender = (string) Character::query()->where('id', $fromCharacterId)->value('gender');
        if ($gender === 'femme') {
            return 'fille';
        }
        if ($gender === 'homme') {
            return 'fils';
        }

        return 'fils/fille';
    }
}
