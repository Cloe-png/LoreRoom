<?php

namespace App\Http\Controllers\Api;

use App\Models\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CharacterApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Character::query()
            ->with(['world:id,name', 'birthPlace:id,name', 'residencePlace:id,name'])
            ->orderBy('name');

        if ($request->filled('world_id')) {
            $query->where('world_id', (int) $request->query('world_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        if ($request->filled('role')) {
            $query->where('role', (string) $request->query('role'));
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('family_name', 'like', '%' . $search . '%');
            });
        }

        return $this->paginated($query->paginate($this->perPage($request))->appends($request->query()));
    }

    public function store(Request $request): JsonResponse
    {
        $character = Character::create($this->validatePayload($request));

        return $this->success(
            $character->load(['world:id,name', 'birthPlace:id,name', 'residencePlace:id,name']),
            [],
            201
        );
    }

    public function show(Character $character): JsonResponse
    {
        return $this->success($character->load([
            'world:id,name',
            'father:id,name',
            'mother:id,name',
            'spouse:id,name',
            'birthPlace:id,name',
            'residencePlace:id,name',
            'species:id,name',
            'factions:id,name',
        ]));
    }

    public function update(Request $request, Character $character): JsonResponse
    {
        $character->update($this->validatePayload($request, true));

        return $this->success($character->fresh()->load(['world:id,name', 'birthPlace:id,name', 'residencePlace:id,name']));
    }

    public function destroy(Character $character): JsonResponse
    {
        $character->delete();

        return response()->json(null, 204);
    }

    private function validatePayload(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'world_id' => [$required, 'integer', 'exists:worlds,id'],
            'name' => [$required, 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'family_name' => ['nullable', 'string', 'max:120'],
            'aliases' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'death_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:100'],
            'has_children' => ['nullable', 'boolean'],
            'has_brother_sister' => ['nullable', 'boolean'],
            'father_id' => ['nullable', 'integer', 'exists:characters,id'],
            'mother_id' => ['nullable', 'integer', 'exists:characters,id'],
            'spouse_id' => ['nullable', 'integer', 'exists:characters,id'],
            'birth_place_id' => ['nullable', 'integer', 'exists:places,id'],
            'residence_place_id' => ['nullable', 'integer', 'exists:places,id'],
            'role' => ['nullable', 'string', 'max:255'],
            'short_term_goal' => ['nullable', 'string'],
            'long_term_goal' => ['nullable', 'string'],
            'secrets' => ['nullable', 'string'],
            'secrets_is_private' => ['nullable', 'boolean'],
            'has_power' => ['nullable', 'boolean'],
            'power_level' => ['nullable', 'integer', 'min:0', 'max:255'],
            'power_description' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'preferred_color' => ['nullable', 'string', 'max:50'],
            'height' => ['nullable', 'string', 'max:50'],
            'silhouette' => ['nullable', 'string', 'max:255'],
            'hair_color' => ['nullable', 'string', 'max:255'],
            'eye_color' => ['nullable', 'string', 'max:255'],
            'hair_eyes' => ['nullable', 'string', 'max:255'],
            'posture' => ['nullable', 'string'],
            'marks' => ['nullable', 'string'],
            'clothing_style' => ['nullable', 'string'],
            'qualities' => ['nullable', 'string'],
            'flaws' => ['nullable', 'string'],
            'psychology_notes' => ['nullable', 'string'],
            'voice_tics' => ['nullable', 'string'],
            'voice_audio_path' => ['nullable', 'string', 'max:255'],
            'voice_youtube_url' => ['nullable', 'url', 'max:255'],
            'summary' => ['nullable', 'string'],
        ]);
    }
}
