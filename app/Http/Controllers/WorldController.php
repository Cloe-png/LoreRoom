<?php

namespace App\Http\Controllers;

use App\Models\World;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorldController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $worlds = $user
            ->worlds()
            ->latest('created_at')
            ->paginate(10);

        return view('manage.worlds.index', [
            'worlds' => $worlds,
            'activeWorldId' => (int) ($user->current_world_id ?? 0),
        ]);
    }

    public function create()
    {
        return view('manage.worlds.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['status'] = 'active';
        $data['summary'] = trim((string) ($data['summary'] ?? '')) ?: null;

        $user = Auth::user();
        $data['user_id'] = (int) $user->id;
        $world = World::create($data);

        if (!(int) ($user->current_world_id ?? 0)) {
            $user->forceFill(['current_world_id' => (int) $world->id])->save();
            $request->session()->put('selected_world_id', (int) $world->id);
        }

        return redirect()->route('manage.worlds.index')->with('success', 'Monde créé.');
    }

    public function switch(Request $request, World $world)
    {
        $user = Auth::user();
        if ((int) $world->user_id !== (int) $user->id) {
            abort(404);
        }

        $user->forceFill(['current_world_id' => (int) $world->id])->save();
        $request->session()->put('selected_world_id', (int) $world->id);

        return redirect()->route('manage.index')->with('success', 'Monde actif changé.');
    }

    public function show(World $world)
    {
        if ((int) $world->user_id !== (int) Auth::id()) {
            abort(404);
        }

        $world->load([
            'characters' => function ($query) {
                $query->orderByRaw('birth_date IS NULL, birth_date ASC')
                    ->orderBy('name');
            },
            'places' => function ($query) {
                $query->orderBy('name');
            },
            'chronicles' => function ($query) {
                $query->orderByRaw('event_date IS NULL, event_date ASC')
                    ->orderBy('title');
            },
        ]);

        $factionsCount = Faction::withoutGlobalScopes()
            ->where('world_id', $world->id)
            ->count();

        $relationsCount = CharacterRelation::query()
            ->whereHas('fromCharacter', function ($query) use ($world) {
                $query->withoutGlobalScopes()->where('world_id', $world->id);
            })
            ->whereHas('toCharacter', function ($query) use ($world) {
                $query->withoutGlobalScopes()->where('world_id', $world->id);
            })
            ->count();

        return view('manage.worlds.show', compact('world', 'factionsCount', 'relationsCount'));
    }

    public function edit(World $world)
    {
        if ((int) $world->user_id !== (int) Auth::id()) {
            abort(404);
        }

        return view('manage.worlds.edit', compact('world'));
    }

    public function update(Request $request, World $world)
    {
        if ((int) $world->user_id !== (int) Auth::id()) {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['name'], $world->id);
        $data['status'] = $world->status ?: 'active';
        $data['summary'] = trim((string) ($data['summary'] ?? '')) ?: null;

        $world->update($data);

        return redirect()->route('manage.worlds.index')->with('success', 'Monde mis à jour.');
    }

    public function destroy(World $world)
    {
        $user = Auth::user();
        if ((int) $world->user_id !== (int) $user->id) {
            abort(404);
        }

        $nextWorldId = (int) ($user->worlds()->where('id', '!=', $world->id)->orderBy('id')->value('id') ?? 0);

        $world->delete();

        $user->refresh();
        $user->forceFill(['current_world_id' => $nextWorldId > 0 ? $nextWorldId : null])->save();
        if ($nextWorldId > 0) {
            request()->session()->put('selected_world_id', $nextWorldId);
        } else {
            request()->session()->forget('selected_world_id');
        }

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
