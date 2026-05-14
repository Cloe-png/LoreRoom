<?php

namespace App\Http\Controllers\Api;

use App\Models\Chronicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChronicleApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Chronicle::query()
            ->with(['world:id,name', 'eventPlace:id,name'])
            ->orderByDesc('event_date')
            ->orderBy('title');

        if ($request->filled('world_id')) {
            $query->where('world_id', (int) $request->query('world_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        if ($request->filled('event_place_id')) {
            $query->where('event_place_id', (int) $request->query('event_place_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('event_date', '>=', (string) $request->query('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('event_date', '<=', (string) $request->query('date_to'));
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        return $this->paginated($query->paginate($this->perPage($request))->appends($request->query()));
    }

    public function store(Request $request): JsonResponse
    {
        $chronicle = Chronicle::create($this->validatePayload($request));

        return $this->success($chronicle->load(['world:id,name', 'eventPlace:id,name']), [], 201);
    }

    public function show(Chronicle $chronicle): JsonResponse
    {
        return $this->success($chronicle->load([
            'world:id,name',
            'eventPlace:id,name',
            'linkedCharacters:id,name',
        ]));
    }

    public function update(Request $request, Chronicle $chronicle): JsonResponse
    {
        $chronicle->update($this->validatePayload($request, true));

        return $this->success($chronicle->fresh()->load(['world:id,name', 'eventPlace:id,name']));
    }

    public function destroy(Chronicle $chronicle): JsonResponse
    {
        $chronicle->delete();

        return response()->json(null, 204);
    }

    private function validatePayload(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'world_id' => [$required, 'integer', 'exists:worlds,id'],
            'title' => [$required, 'string', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'event_place_id' => ['nullable', 'integer', 'exists:places,id'],
            'event_location' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
