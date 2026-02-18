<?php

namespace App\Http\Controllers;

use App\Models\World;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorldController extends Controller
{
    public function index()
    {
        $worlds = World::latest()->paginate(10);

        return view('manage.worlds.index', compact('worlds'));
    }

    public function create()
    {
        return view('manage.worlds.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'geography_type' => ['required', Rule::in(['ile', 'ville', 'pays'])],
            'map' => ['nullable', 'image', 'max:6144'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['status'] = 'active';
        $data['summary'] = null;

        if ($request->hasFile('map')) {
            $data['map_path'] = $request->file('map')->store('worlds', 'public');
        }

        World::create($data);

        return redirect()->route('manage.worlds.index')->with('success', 'Monde cree.');
    }

    public function show(World $world)
    {
        $world->load(['characters', 'places', 'chronicles']);

        return view('manage.worlds.show', compact('world'));
    }

    public function edit(World $world)
    {
        return view('manage.worlds.edit', compact('world'));
    }

    public function update(Request $request, World $world)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'geography_type' => ['required', Rule::in(['ile', 'ville', 'pays'])],
            'map' => ['nullable', 'image', 'max:6144'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['name'], $world->id);
        $data['status'] = $world->status ?: 'active';
        $data['summary'] = $world->summary;

        if ($request->hasFile('map')) {
            if ($world->map_path) {
                Storage::disk('public')->delete($world->map_path);
            }
            $data['map_path'] = $request->file('map')->store('worlds', 'public');
        }

        $world->update($data);

        return redirect()->route('manage.worlds.index')->with('success', 'Monde mis à jour.');
    }

    public function destroy(World $world)
    {
        if ($world->map_path) {
            Storage::disk('public')->delete($world->map_path);
        }
        $world->delete();

        return redirect()->route('manage.worlds.index')->with('success', 'Monde supprimé.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $prefix = $base !== '' ? $base : 'monde';
        $slug = $prefix;
        $i = 2;

        while (
            World::query()
                ->when($ignoreId, function ($q) use ($ignoreId) {
                    $q->where('id', '!=', $ignoreId);
                })
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $prefix . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
