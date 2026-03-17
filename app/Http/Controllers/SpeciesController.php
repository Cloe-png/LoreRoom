<?php

namespace App\Http\Controllers;

use App\Models\Species;
use Illuminate\Http\Request;

class SpeciesController extends Controller
{
    public function index()
    {
        $q = trim(request('q', ''));

        $species = Species::with('world')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('characteristics', 'like', $like)
                        ->orWhere('abilities', 'like', $like)
                        ->orWhere('lifespan', 'like', $like)
                        ->orWhere('origin', 'like', $like);
                });
            })
            ->latest()
            ->paginate(10)
            ->appends(['q' => $q]);

        return view('manage.species.index', compact('species', 'q'));
    }

    public function create()
    {
        $defaultWorld = $this->currentWorld();

        return view('manage.species.create', compact('defaultWorld'));
    }

    public function store(Request $request)
    {
        $worldId = $this->requireCurrentWorldId();
        if (!$worldId) {
            return back()->withErrors(['world' => "Créez d'abord un monde."])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'characteristics' => ['nullable', 'string', 'max:4000'],
            'abilities' => ['nullable', 'string', 'max:4000'],
            'lifespan' => ['nullable', 'string', 'max:120'],
            'origin' => ['nullable', 'string', 'max:4000'],
        ]);
        $data['world_id'] = $worldId;

        $species = Species::create($data);

        return redirect()->route('manage.species.show', $species)->with('success', 'Espèce créée.');
    }

    public function show(Species $species)
    {
        $this->abortIfOutsideCurrentWorld((int) $species->world_id);
        $species->load(['world', 'characters']);

        return view('manage.species.show', compact('species'));
    }

    public function edit(Species $species)
    {
        $this->abortIfOutsideCurrentWorld((int) $species->world_id);
        $defaultWorld = $this->currentWorld();

        return view('manage.species.edit', compact('species', 'defaultWorld'));
    }

    public function update(Request $request, Species $species)
    {
        $this->abortIfOutsideCurrentWorld((int) $species->world_id);
        $worldId = $this->requireCurrentWorldId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'characteristics' => ['nullable', 'string', 'max:4000'],
            'abilities' => ['nullable', 'string', 'max:4000'],
            'lifespan' => ['nullable', 'string', 'max:120'],
            'origin' => ['nullable', 'string', 'max:4000'],
        ]);
        $data['world_id'] = $worldId;

        $species->update($data);

        return redirect()->route('manage.species.show', $species)->with('success', 'Espèce mise à jour.');
    }

    public function destroy(Species $species)
    {
        $this->abortIfOutsideCurrentWorld((int) $species->world_id);

        $species->delete();

        return redirect()->route('manage.species.index')->with('success', 'Espèce supprimée.');
    }
}
