<?php

namespace App\Http\Controllers;

use App\Models\ImaginaryMap;
use App\Models\World;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ImaginaryMapController extends Controller
{
    public function index()
    {
        $maps = ImaginaryMap::with('world')->latest()->paginate(10);

        return view('manage.maps.index', compact('maps'));
    }

    public function create()
    {
        $defaultWorld = World::query()->orderBy('id')->first();

        return view('manage.maps.create', compact('defaultWorld'));
    }

    public function store(Request $request)
    {
        $defaultWorldId = World::query()->value('id');
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => "Créez d'abord un monde."])->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'map_type' => ['nullable', 'string', 'max:80'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);
        $data['world_id'] = $defaultWorldId;

        ImaginaryMap::create($data);

        return redirect()->route('manage.maps.index')->with('success', 'Carte imaginaire créée.');
    }

    public function show(ImaginaryMap $map)
    {
        $map->load('world');

        return view('manage.maps.show', compact('map'));
    }

    public function edit(ImaginaryMap $map)
    {
        $defaultWorld = World::query()->orderBy('id')->first();

        return view('manage.maps.edit', compact('map', 'defaultWorld'));
    }

    public function update(Request $request, ImaginaryMap $map)
    {
        $defaultWorldId = World::query()->value('id');
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => "Créez d'abord un monde."])->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'map_type' => ['nullable', 'string', 'max:80'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);
        $data['world_id'] = $defaultWorldId;

        $map->update($data);

        return redirect()->route('manage.maps.index')->with('success', 'Carte imaginaire mise à jour.');
    }

    public function destroy(ImaginaryMap $map)
    {
        $map->delete();

        return redirect()->route('manage.maps.index')->with('success', 'Carte imaginaire supprimée.');
    }
}

