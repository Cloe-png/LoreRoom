<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\World;
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
        $defaultWorld = World::query()->orderBy('id')->first();

        return view('manage.places.create', compact('defaultWorld'));
    }

    public function store(Request $request)
    {
        $defaultWorldId = World::query()->value('id');
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => 'Créez d’abord un monde.'])->withInput();
        }

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
        $place->load('world');

        return view('manage.places.show', compact('place'));
    }

    public function edit(Place $place)
    {
        $defaultWorld = World::query()->orderBy('id')->first();

        return view('manage.places.edit', compact('place', 'defaultWorld'));
    }

    public function update(Request $request, Place $place)
    {
        $defaultWorldId = World::query()->value('id');
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => 'Créez d’abord un monde.'])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ]);
        $data['world_id'] = $defaultWorldId;

        $place->update($data);

        return redirect()->route('manage.places.index')->with('success', 'Lieu mis à jour.');
    }

    public function destroy(Place $place)
    {
        $place->delete();

        return redirect()->route('manage.places.index')->with('success', 'Lieu supprimé.');
    }
}
