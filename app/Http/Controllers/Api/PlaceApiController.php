<?php

namespace App\Http\Controllers\Api;

use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaceApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Place::query()
            ->with('world:id,name')
            ->orderBy('name');

        if ($request->filled('world_id')) {
            $query->where('world_id', (int) $request->query('world_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', (string) $request->query('type'));
        }

        if ($request->filled('region')) {
            $query->where('region', 'like', '%' . trim((string) $request->query('region')) . '%');
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%');
            });
        }

        return $this->paginated($query->paginate($this->perPage($request))->appends($request->query()));
    }

    public function store(Request $request): JsonResponse
    {
        $place = Place::create($this->validatePayload($request));

        return $this->success($place->load('world:id,name'), [], 201);
    }

    public function show(Place $place): JsonResponse
    {
        return $this->success($place->load([
            'world:id,name',
            'galleryImages:id,place_id,image_path,caption,sort_order',
        ]));
    }

    public function update(Request $request, Place $place): JsonResponse
    {
        $place->update($this->validatePayload($request, true));

        return $this->success($place->fresh()->load('world:id,name'));
    }

    public function destroy(Place $place): JsonResponse
    {
        $place->delete();

        return response()->json(null, 204);
    }

    private function validatePayload(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'world_id' => [$required, 'integer', 'exists:worlds,id'],
            'name' => [$required, 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:40'],
            'region' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
