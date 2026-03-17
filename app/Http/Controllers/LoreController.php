<?php

namespace App\Http\Controllers;

use App\Models\LoreEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoreController extends Controller
{
    private const CATEGORIES = [
        'religion',
        'culture',
        'race',
        'technologie',
        'règle du monde',
        'politique',
        'mythologie',
        'autre',
    ];

    public function index()
    {
        $q = trim(request('q', ''));
        $category = trim(request('category', ''));

        $entries = LoreEntry::with('world')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('title', 'like', $like)
                        ->orWhere('content', 'like', $like)
                        ->orWhere('tags', 'like', $like);
                });
            })
            ->when($category !== '', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->latest()
            ->paginate(12)
            ->appends(['q' => $q, 'category' => $category]);

        $categoryOptions = self::CATEGORIES;

        return view('manage.lore.index', compact('entries', 'q', 'category', 'categoryOptions'));
    }

    public function create()
    {
        $defaultWorld = $this->currentWorld();
        $categoryOptions = self::CATEGORIES;

        return view('manage.lore.create', compact('defaultWorld', 'categoryOptions'));
    }

    public function store(Request $request)
    {
        $worldId = $this->requireCurrentWorldId();
        if (!$worldId) {
            return back()->withErrors(['world' => "Créez d'abord un monde."])->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'category' => ['nullable', Rule::in(self::CATEGORIES)],
            'content' => ['nullable', 'string', 'max:10000'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);
        $data['world_id'] = $worldId;

        $entry = LoreEntry::create($data);

        return redirect()->route('manage.lore.show', $entry)->with('success', 'Entrée de lore créée.');
    }

    public function show(LoreEntry $lore)
    {
        $this->abortIfOutsideCurrentWorld((int) $lore->world_id);
        $lore->load('world');

        return view('manage.lore.show', ['entry' => $lore]);
    }

    public function edit(LoreEntry $lore)
    {
        $this->abortIfOutsideCurrentWorld((int) $lore->world_id);
        $defaultWorld = $this->currentWorld();
        $categoryOptions = self::CATEGORIES;

        return view('manage.lore.edit', [
            'entry' => $lore,
            'defaultWorld' => $defaultWorld,
            'categoryOptions' => $categoryOptions,
        ]);
    }

    public function update(Request $request, LoreEntry $lore)
    {
        $this->abortIfOutsideCurrentWorld((int) $lore->world_id);
        $worldId = $this->requireCurrentWorldId();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'category' => ['nullable', Rule::in(self::CATEGORIES)],
            'content' => ['nullable', 'string', 'max:10000'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);
        $data['world_id'] = $worldId;

        $lore->update($data);

        return redirect()->route('manage.lore.show', $lore)->with('success', 'Entrée de lore mise à jour.');
    }

    public function destroy(LoreEntry $lore)
    {
        $this->abortIfOutsideCurrentWorld((int) $lore->world_id);

        $lore->delete();

        return redirect()->route('manage.lore.index')->with('success', 'Entrée de lore supprimée.');
    }
}
