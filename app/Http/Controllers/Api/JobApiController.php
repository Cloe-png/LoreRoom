<?php

namespace App\Http\Controllers\Api;

use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Job::query()
            ->with('world:id,name')
            ->orderBy('name');

        if ($request->filled('world_id')) {
            $query->where('world_id', (int) $request->query('world_id'));
        }

        if ($request->has('is_default')) {
            $query->where('is_default', filter_var($request->query('is_default'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        return $this->paginated($query->paginate($this->perPage($request))->appends($request->query()));
    }

    public function store(Request $request): JsonResponse
    {
        $job = Job::create($this->validatePayload($request));

        return $this->success($job->load('world:id,name'), [], 201);
    }

    public function show(Job $job): JsonResponse
    {
        return $this->success($job->load('world:id,name'));
    }

    public function update(Request $request, Job $job): JsonResponse
    {
        $job->update($this->validatePayload($request, true));

        return $this->success($job->fresh()->load('world:id,name'));
    }

    public function destroy(Job $job): JsonResponse
    {
        $job->delete();

        return response()->json(null, 204);
    }

    private function validatePayload(Request $request, bool $isUpdate = false): array
    {
        $required = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'world_id' => ['nullable', 'integer', 'exists:worlds,id'],
            'name' => [$required, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}
