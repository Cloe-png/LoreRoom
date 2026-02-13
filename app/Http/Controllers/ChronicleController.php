<?php

namespace App\Http\Controllers;

use App\Models\Chronicle;
use App\Models\World;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChronicleController extends Controller
{
    public function index()
    {
        $chronicles = Chronicle::with('world')->latest()->paginate(10);

        return view('manage.chronicles.index', compact('chronicles'));
    }

    public function create()
    {
        $defaultWorld = World::query()->orderBy('id')->first();

        return view('manage.chronicles.create', compact('defaultWorld'));
    }

    public function store(Request $request)
    {
        $defaultWorldId = World::query()->value('id');
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => 'Créez d’abord un monde.'])->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'event_date' => ['nullable', 'date'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);
        $data['world_id'] = $defaultWorldId;

        Chronicle::create($data);

        return redirect()->route('manage.chronicles.index')->with('success', 'Chronique creee.');
    }

    public function show(Chronicle $chronicle)
    {
        $chronicle->load('world');

        return view('manage.chronicles.show', compact('chronicle'));
    }

    public function edit(Chronicle $chronicle)
    {
        $defaultWorld = World::query()->orderBy('id')->first();

        return view('manage.chronicles.edit', compact('chronicle', 'defaultWorld'));
    }

    public function update(Request $request, Chronicle $chronicle)
    {
        $defaultWorldId = World::query()->value('id');
        if (!$defaultWorldId) {
            return back()->withErrors(['world' => 'Créez d’abord un monde.'])->withInput();
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'event_date' => ['nullable', 'date'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);
        $data['world_id'] = $defaultWorldId;

        $chronicle->update($data);

        return redirect()->route('manage.chronicles.index')->with('success', 'Chronique mise à jour.');
    }

    public function destroy(Chronicle $chronicle)
    {
        $chronicle->delete();

        return redirect()->route('manage.chronicles.index')->with('success', 'Chronique supprimée.');
    }
}
