<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        $places = Place::with('world')->latest()->paginate(10);

        return view('manage.places.index', compact('places'));
    }

    public function create()
    {
        $defaultWorld = $this->currentWorld();

        return view('manage.places.create', compact('defaultWorld'));
    }

    public function store(Request $request)
    {
        $defaultWorldId = $this->requireCurrentWorldId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ]);
        $data['world_id'] = $defaultWorldId;

        Place::create($data);

        return redirect()->route('manage.places.index')->with('success', 'Lieu cree.');
    }

    public function show(Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $place->load('world');

        return view('manage.places.show', compact('place'));
    }

    public function edit(Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $defaultWorld = $this->currentWorld();

        return view('manage.places.edit', compact('place', 'defaultWorld'));
    }

    public function update(Request $request, Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $defaultWorldId = $this->requireCurrentWorldId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ]);
        $data['world_id'] = $defaultWorldId;

        $place->update($data);

        return redirect()->route('manage.places.index')->with('success', 'Lieu mis a jour.');
    }

    public function destroy(Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $place->delete();

        return redirect()->route('manage.places.index')->with('success', 'Lieu supprime.');
    }
}
