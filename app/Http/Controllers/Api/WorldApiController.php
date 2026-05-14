<?php

namespace App\Http\Controllers\Api;

use App\Models\World;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorldApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = World::query()
            ->select('id', 'user_id', 'name', 'slug', 'summary', 'status')
            ->with('user:id,name,email')
            ->withCount(['characters', 'places', 'chronicles'])
            ->orderBy('name')
            ;

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->query('user_id'));
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%');
            });
        }

        return $this->paginated($query->paginate($this->perPage($request))->appends($request->query()));
    }

    public function store(Request $request): JsonResponse
    {
        $world = World::create($this->validatePayload($request));

        return $this->success($world->load('user:id,name,email'), [], 201);
    }

    public function show(World $world): JsonResponse
    {
        return $this->success($world->load('user:id,name,email')->loadCount(['characters', 'places', 'chronicles']));
    }

    public function update(Request $request, World $world): JsonResponse
    {
        $world->update($this->validatePayload($request, true, $world));

        return $this->success($world->fresh()->load('user:id,name,email'));
    }

    public function destroy(World $world): JsonResponse
    {
        $world->delete();

        return response()->json(null, 204);
    }

    public function characters(World $world): JsonResponse
    {
        return $this->success(
            $world->characters()
                ->select('id', 'world_id', 'name', 'role', 'status', 'birth_date', 'death_date')
                ->orderBy('name')
                ->get(),
            [
                'world' => [
                    'id' => $world->id,
                    'name' => $world->name,
                ],
            ]
        );
    }

    public function stats(World $world): JsonResponse
    {
        $characterCount = DB::selectOne(
            'SELECT fn_world_character_count(?) AS total',
            [$world->id]
        );

        $dashboard = DB::selectOne('CALL sp_world_dashboard(?)', [$world->id]);

        return response()->json([
            'data' => [
                'world' => [
                    'id' => $world->id,
                    'name' => $world->name,
                ],
                'function_result' => [
                    'character_count' => (int) ($characterCount->total ?? 0),
                ],
                'procedure_result' => $dashboard,
            ],
        ]);
    }

    private function validatePayload(Request $request, bool $isUpdate = false, ?World $world = null): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';
        $worldId = $world ? $world->id : null;

        return $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => [$required, 'string', 'max:255'],
            'slug' => [
                $required,
                'string',
                'max:255',
                Rule::unique('worlds', 'slug')->ignore($worldId),
            ],
            'summary' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:100'],
        ]);
    }
};
