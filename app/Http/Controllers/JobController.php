<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $q = trim(request('q', ''));
        $worldId = $this->currentWorldId();

        $jobs = Job::with('world')
            ->when($worldId, function ($query) use ($worldId) {
                $query->whereNull('world_id')->orWhere('world_id', $worldId);
            }, function ($query) {
                $query->whereNull('world_id');
            })
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->paginate(20)
            ->appends(['q' => $q]);

        return view('manage.jobs.index', compact('jobs', 'q'));
    }

    public function create()
    {
        $defaultWorld = $this->currentWorld();

        return view('manage.jobs.create', compact('defaultWorld'));
    }

    public function store(Request $request)
    {
        $worldId = $this->requireCurrentWorldId();
        if (!$worldId) {
            return back()->withErrors(['world' => "Créez d'abord un monde."])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['world_id'] = $worldId;
        $data['is_default'] = false;

        $job = Job::create($data);

        return redirect()->route('manage.jobs.show', $job)->with('success', 'Métier créé.');
    }

    public function show(Job $job)
    {
        if ($job->world_id) {
            $this->abortIfOutsideCurrentWorld((int) $job->world_id);
        }
        $job->load(['world', 'characters']);

        return view('manage.jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        if ($job->is_default) {
            return redirect()->route('manage.jobs.index')->withErrors(['job' => 'Les métiers par défaut ne sont pas éditables.']);
        }

        $this->abortIfOutsideCurrentWorld((int) $job->world_id);
        $defaultWorld = $this->currentWorld();

        return view('manage.jobs.edit', compact('job', 'defaultWorld'));
    }

    public function update(Request $request, Job $job)
    {
        if ($job->is_default) {
            return redirect()->route('manage.jobs.index')->withErrors(['job' => 'Les métiers par défaut ne sont pas éditables.']);
        }

        $this->abortIfOutsideCurrentWorld((int) $job->world_id);
        $worldId = $this->requireCurrentWorldId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['world_id'] = $worldId;

        $job->update($data);

        return redirect()->route('manage.jobs.show', $job)->with('success', 'Métier mis à jour.');
    }

    public function destroy(Job $job)
    {
        if ($job->is_default) {
            return redirect()->route('manage.jobs.index')->withErrors(['job' => 'Les métiers par défaut ne sont pas supprimables.']);
        }

        $this->abortIfOutsideCurrentWorld((int) $job->world_id);
        $job->delete();

        return redirect()->route('manage.jobs.index')->with('success', 'Métier supprimé.');
    }
}
