<?php

namespace App\Http\Controllers\Api;

use App\Models\CharacterRelation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacterRelationApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = CharacterRelation::query()
            ->with([
                'fromCharacter:id,world_id,name',
                'toCharacter:id,world_id,name',
            ])
            ->orderByDesc('id');

        if ($request->filled('from_character_id')) {
            $query->where('from_character_id', (int) $request->query('from_character_id'));
        }

        if ($request->filled('to_character_id')) {
            $query->where('to_character_id', (int) $request->query('to_character_id'));
        }

        if ($request->filled('world_id')) {
            $worldId = (int) $request->query('world_id');
            $query->whereHas('fromCharacter', function ($builder) use ($worldId) {
                $builder->where('world_id', $worldId);
            });
        }

        if ($request->filled('relation_type')) {
            $query->where('relation_type', (string) $request->query('relation_type'));
        }

        if ($request->filled('relation_category')) {
            $query->where('relation_category', (string) $request->query('relation_category'));
        }

        return $this->paginated($query->paginate($this->perPage($request))->appends($request->query()));
    }

    public function store(Request $request): JsonResponse
    {
        $relation = CharacterRelation::create($this->validatePayload($request));

        return $this->success($relation->load(['fromCharacter:id,name', 'toCharacter:id,name']), [], 201);
    }

    public function show(CharacterRelation $relation): JsonResponse
    {
        return $this->success($relation->load(['fromCharacter:id,world_id,name', 'toCharacter:id,world_id,name']));
    }

    public function update(Request $request, CharacterRelation $relation): JsonResponse
    {
        $relation->update($this->validatePayload($request, true));

        return $this->success($relation->fresh()->load(['fromCharacter:id,name', 'toCharacter:id,name']));
    }

    public function destroy(CharacterRelation $relation): JsonResponse
    {
        $relation->delete();

        return response()->json(null, 204);
    }

    private function validatePayload(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'from_character_id' => [$required, 'integer', 'exists:characters,id'],
            'to_character_id' => [$required, 'integer', 'exists:characters,id'],
            'relation_type' => [$required, 'string', 'max:255'],
            'relation_category' => ['nullable', 'string', 'max:40'],
            'sibling_kind' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string'],
            'intensity' => ['nullable', 'integer', 'min:0', 'max:255'],
            'is_bidirectional' => ['nullable', 'boolean'],
        ]);
    }
}
