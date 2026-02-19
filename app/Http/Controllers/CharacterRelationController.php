<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CharacterRelationController extends Controller
{
    public function index()
    {
        $q = trim(request('q', ''));
        $selectedCharacterId = (int) request('character_id', 0);
        $characters = Character::orderBy('name')->get(['id', 'name', 'first_name', 'last_name']);

        $relations = CharacterRelation::with([
                'fromCharacter.primaryGalleryImage',
                'toCharacter.primaryGalleryImage',
            ])
            ->when($selectedCharacterId > 0, function ($query) use ($selectedCharacterId) {
                $query->where(function ($sub) use ($selectedCharacterId) {
                    $sub->where('from_character_id', $selectedCharacterId)
                        ->orWhere('to_character_id', $selectedCharacterId);
                });
            })
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
            ->appends(['q' => $q, 'character_id' => $selectedCharacterId ?: null]);

        $relations->getCollection()->transform(function (CharacterRelation $relation) {
            $relation->display_type = $this->resolveDisplayRelationType($relation);
            $relation->from_photo = $this->resolveCharacterPhotoPath($relation->fromCharacter);
            $relation->to_photo = $this->resolveCharacterPhotoPath($relation->toCharacter);
            $relation->from_color = optional($relation->fromCharacter)->preferred_color;
            $relation->to_color = optional($relation->toCharacter)->preferred_color;
            return $relation;
        });

        $graphRelations = CharacterRelation::with([
                'fromCharacter.primaryGalleryImage',
                'toCharacter.primaryGalleryImage',
            ])
            ->when($selectedCharacterId > 0, function ($query) use ($selectedCharacterId) {
                $query->where(function ($sub) use ($selectedCharacterId) {
                    $sub->where('from_character_id', $selectedCharacterId)
                        ->orWhere('to_character_id', $selectedCharacterId);
                });
            })
            ->latest()
            ->take(120)
            ->get()
            ->map(function ($relation) {
                $from = $relation->fromCharacter;
                $to = $relation->toCharacter;

                return [
                    'from_id' => $relation->from_character_id,
                    'to_id' => $relation->to_character_id,
                    'from' => optional($from)->display_name,
                    'from_status' => optional($from)->status,
                    'from_color' => optional($from)->preferred_color,
                    'from_photo' => $this->resolveCharacterPhotoPath($from),
                    'to' => optional($to)->display_name,
                    'to_status' => optional($to)->status,
                    'to_color' => optional($to)->preferred_color,
                    'to_photo' => $this->resolveCharacterPhotoPath($to),
                    'type' => $this->resolveDisplayRelationType($relation),
                    'bidirectional' => $relation->is_bidirectional,
                ];
            });

        return view('manage.relations.index', compact(
            'relations',
            'graphRelations',
            'q',
            'characters',
            'selectedCharacterId'
        ));
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
            'is_bidirectional' => ['nullable', 'boolean'],
        ]);

        $data['is_bidirectional'] = $request->boolean('is_bidirectional');
        $data['relation_type'] = $this->normalizeChildTypeBySourceGender($data['relation_type'], (int) $data['from_character_id']);
        $data = $this->assignRelationMetadata($data);

        CharacterRelation::create($data);

        return redirect()->route('manage.relations.index')->with('success', 'Relation créée.');
    }

    public function show(CharacterRelation $relation)
    {
        $relation->load(['fromCharacter.primaryGalleryImage', 'toCharacter.primaryGalleryImage']);
        $relation->display_type = $this->resolveDisplayRelationType($relation);
        $relation->from_photo = $this->resolveCharacterPhotoPath($relation->fromCharacter);
        $relation->to_photo = $this->resolveCharacterPhotoPath($relation->toCharacter);

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
            'is_bidirectional' => ['nullable', 'boolean'],
        ]);

        $data['is_bidirectional'] = $request->boolean('is_bidirectional');
        $data['relation_type'] = $this->normalizeChildTypeBySourceGender($data['relation_type'], (int) $data['from_character_id']);
        $data = $this->assignRelationMetadata($data);

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

    private function resolveCharacterPhotoPath(?Character $character): ?string
    {
        if (!$character) {
            return null;
        }

        if (empty($character->image_path)) {
            return null;
        }

        $path = (string) $character->image_path;

        return Storage::disk('public')->exists($path) ? $path : null;
    }

    private function assignRelationMetadata(array $data): array
    {
        [$category, $siblingKind] = $this->inferRelationMetadata((string) ($data['relation_type'] ?? ''));
        $data['relation_category'] = $category;
        $data['sibling_kind'] = $siblingKind;

        return $data;
    }

    private function inferRelationMetadata(string $relationType): array
    {
        $type = mb_strtolower(trim($relationType));
        $category = 'custom';
        $siblingKind = null;

        if (in_array($type, ['pere', 'mere', 'fils', 'fille', 'fils/fille', 'parent de', 'enfant de'], true)) {
            $category = 'family_lineage';
        } elseif (in_array($type, ['frere', 'soeur', 'frere/soeur'], true)) {
            $category = 'family_sibling';
            $siblingKind = 'full';
        } elseif (in_array($type, ['demi-frere', 'demi-soeur', 'demi-frere/soeur'], true)) {
            $category = 'family_sibling';
            $siblingKind = 'half';
        } elseif (in_array($type, ['jumeau', 'jumelle', 'jumeaux'], true)) {
            $category = 'family_sibling';
            $siblingKind = 'twin';
        } elseif (in_array($type, ['epoux', 'epouse', 'epoux/epouse', 'amour'], true)) {
            $category = 'family_couple';
        } elseif (in_array($type, ['ami', 'allie', 'ennemi', 'mentor', 'rival'], true)) {
            $category = 'social';
        }

        return [$category, $siblingKind];
    }
}

