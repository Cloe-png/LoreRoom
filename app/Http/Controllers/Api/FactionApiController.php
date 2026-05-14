<?php

namespace App\Http\Controllers\Api;

use App\Models\Faction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FactionApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Faction::query()
            ->with(['world:id,name', 'leader:id,name', 'coLeader:id,name', 'founder:id,name'])
            ->orderBy('name');

        if ($request->filled('world_id')) {
            $query->where('world_id', (int) $request->query('world_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', (string) $request->query('type'));
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')
                    ->orWhere('motto', 'like', '%' . $search . '%');
            });
        }

        return $this->paginated($query->paginate($this->perPage($request))->appends($request->query()));
    }

    public function store(Request $request): JsonResponse
    {
        $faction = Faction::create($this->validatePayload($request));

        return $this->success($faction->load(['world:id,name', 'leader:id,name', 'coLeader:id,name', 'founder:id,name']), [], 201);
    }

    public function show(Faction $faction): JsonResponse
    {
        return $this->success($faction->load([
            'world:id,name',
            'leader:id,name',
            'coLeader:id,name',
            'founder:id,name',
            'members:id,name',
        ]));
    }

    public function update(Request $request, Faction $faction): JsonResponse
    {
        $faction->update($this->validatePayload($request, true));

        return $this->success($faction->fresh()->load(['world:id,name', 'leader:id,name', 'coLeader:id,name', 'founder:id,name']));
    }

    public function destroy(Faction $faction): JsonResponse
    {
        $faction->delete();

        return response()->json(null, 204);
    }

    private function validatePayload(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'world_id' => [$required, 'integer', 'exists:worlds,id'],
            'name' => [$required, 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'motto' => ['nullable', 'string', 'max:255'],
            'founded_at' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:255'],
            'leader_id' => ['nullable', 'integer', 'exists:characters,id'],
            'co_leader_id' => ['nullable', 'integer', 'exists:characters,id'],
            'founder_id' => ['nullable', 'integer', 'exists:characters,id'],
            'logo_path' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
