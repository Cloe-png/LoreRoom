<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Models\Chapter;
use App\Models\Character;
use App\Models\Chronicle;
use App\Models\Faction;
use App\Models\Place;
use App\Models\Species;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PlaceController extends Controller
{
    private const TYPES = ['ville','village','forteresse','foret','montagne','taverne','temple','ruines','region','royaume','continent','dimension','autre'];
    private const STATUSES = ['prospere','en_ruine','disparu','cache','conteste','inconnu'];

    public function index(Request $request)
    {
        $this->authorize('viewAny', Place::class);
        $places = Place::query()->when($request->filled('q'), fn ($q) => $q->where(fn ($s) => $s->where('name','like','%'.$request->q.'%')->orWhere('short_description','like','%'.$request->q.'%')))
            ->when($request->filled('type'), fn ($q) => $q->where('type',$request->type))
            ->when($request->filled('status'), fn ($q) => $q->where('status',$request->status))
            ->with('parent')->orderBy('name')->get();
        $tree = $this->tree($places);
        return view('manage.places.index', ['places'=>$places,'tree'=>$tree,'types'=>self::TYPES,'statuses'=>self::STATUSES]);
    }

    public function create()
    {
        $this->authorize('create', Place::class);
        return view('manage.places.create', $this->formData());
    }

    public function store(StorePlaceRequest $request)
    {
        $worldId = $this->requireCurrentWorldId();
        $data = $this->validatedData($request->validated());
        $data['world_id'] = $worldId;
        if ($request->hasFile('image')) $data['image_path'] = $request->file('image')->store('places', 'public');
        $quickOnly = !$request->boolean('complete');
        $place = DB::transaction(function () use ($data, $request) {
            $place = Place::create($data);
            $this->syncRelations($place, $request);
            return $place;
        });
        UserLog::logAction((int)$request->user()->id, 'lieu_cree', 'manage.places.store', (string)$place->id);
        return $quickOnly
            ? redirect()->route('manage.places.index')->with('success', 'Lieu créé.')
            : redirect()->route('manage.places.edit', $place)->with('success', 'Lieu créé. Complétez sa fiche.');
    }

    public function show(Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $this->authorize('view', $place);
        $place->load(['parent','children','connectedPlaces','connectedFromPlaces','leaders','factions','species','characters','chronicles','chapters','pointsOfInterest','galleryImages','eventChronicles','birthCharacters','residentCharacters']);
        $place->setRelation('connectedPlaces', $place->connectedPlaces->merge($place->connectedFromPlaces)->unique('id')->values());
        $ancestors = collect(); $parent = $place->parent; $seen = [];
        while ($parent && !isset($seen[$parent->id])) { $ancestors->prepend($parent); $seen[$parent->id] = true; $parent = $parent->parent; }
        return view('manage.places.show', compact('place','ancestors'));
    }

    public function edit(Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $this->authorize('update', $place);
        $place->load(['connectedPlaces','connectedFromPlaces','leaders','factions','species','characters','chronicles','chapters','pointsOfInterest','galleryImages']);
        $place->setRelation('connectedPlaces', $place->connectedPlaces->merge($place->connectedFromPlaces)->unique('id')->values());
        return view('manage.places.edit', array_merge($this->formData($place), ['place'=>$place]));
    }

    public function update(UpdatePlaceRequest $request, Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $data = $this->validatedData($request->validated());
        if ($request->hasFile('image')) {
            if ($place->image_path) Storage::disk('public')->delete($place->image_path);
            $data['image_path'] = $request->file('image')->store('places', 'public');
        }
        DB::transaction(function () use ($place, $data, $request) { $place->update($data); $this->syncRelations($place, $request); });
        UserLog::logAction((int)$request->user()->id, 'lieu_modifie', 'manage.places.update', (string)$place->id);
        return redirect()->route('manage.places.show', $place)->with('success', 'Lieu mis à jour.');
    }

    public function destroy(Request $request, Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $this->authorize('delete', $place);
        DB::transaction(function () use ($place) { $place->children()->update(['parent_id'=>$place->parent_id]); $place->delete(); });
        UserLog::logAction((int)$request->user()->id, 'lieu_supprime', 'manage.places.destroy', (string)$place->id);
        return redirect()->route('manage.places.index')->with('success', 'Lieu supprimé.');
    }

    public function duplicate(Place $place)
    {
        $this->authorize('view', $place);
        $copy = $place->replicate(['name','created_at','updated_at','deleted_at']);
        $copy->name = $place->name.' (copie)'; $copy->parent_id = $place->parent_id; $copy->save();
        return redirect()->route('manage.places.edit', $copy)->with('success', 'Copie créée.');
    }

    private function formData(?Place $place = null): array
    {
        $worldId = $this->requireCurrentWorldId();
        return [
            'defaultWorld'=>$this->currentWorld(), 'placeTypeOptions'=>self::TYPES, 'statusOptions'=>self::STATUSES,
            'parentOptions'=>Place::where('world_id',$worldId)->when($place,fn($q)=>$q->where('id','!=',$place->id))->orderBy('name')->get(['id','name','parent_id']),
            'characters'=>Character::where('world_id',$worldId)->orderBy('name')->get(['id','name','first_name','last_name']),
            'factions'=>Faction::where('world_id',$worldId)->orderBy('name')->get(['id','name']),
            'speciesOptions'=>Species::where('world_id',$worldId)->orderBy('name')->get(['id','name']),
            'chronicles'=>Chronicle::where('world_id',$worldId)->orderBy('title')->get(['id','title']),
            'chapters'=>Chapter::where('world_id',$worldId)->orderBy('title')->get(['id','title']),
        ];
    }

    private function validatedData(array $data): array
    {
        unset($data['image'], $data['connected_places'], $data['leaders'], $data['factions'], $data['species'], $data['characters'], $data['chronicles'], $data['chapters'], $data['travel_notes'], $data['leader_titles'], $data['points']);
        foreach (['description','short_description','economy','dangers','history','private_notes'] as $field) if (isset($data[$field])) $data[$field] = trim(strip_tags($data[$field]));
        foreach (['atmosphere','alt_names','languages'] as $field) if (isset($data[$field])) $data[$field] = array_values(array_filter(array_map('trim',$data[$field]), fn($v)=>$v!==''));
        return $data;
    }

    private function syncRelations(Place $place, Request $request): void
    {
        $idList = fn($key) => array_values(array_unique(array_map('intval', (array)$request->input($key, []))));
        $connected = collect($idList('connected_places'))->reject(fn($id)=>(int)$place->id===$id)->all();
        $place->connectedPlaces()->detach();
        $place->connectedFromPlaces()->detach();
        $travelPivots = collect($connected)->mapWithKeys(fn($id,$i)=>[$id=>['travel_note'=>$request->input('travel_notes.'.$id) ?? $request->input('travel_notes.'.$i) ?? $request->input('travel_notes.0')]]);
        $place->connectedPlaces()->sync($travelPivots);
        foreach ($travelPivots as $id => $pivot) {
            Place::whereKey($id)->first()->connectedPlaces()->syncWithoutDetaching([$place->id => $pivot]);
        }
        $place->leaders()->sync(collect($idList('leaders'))->mapWithKeys(fn($id)=>[$id=>['title'=>$request->input('leader_titles.'.$id)]]));
        foreach (['factions','species','characters','chronicles','chapters'] as $relation) $place->{$relation}()->sync($idList($relation));
        $place->pointsOfInterest()->delete();
        foreach ((array)$request->input('points', []) as $position=>$point) if (trim($point['name'] ?? '') !== '') $place->pointsOfInterest()->create(['name'=>trim($point['name']),'description'=>trim(strip_tags($point['description'] ?? '')),'position'=>$position]);
    }

    private function tree($places): array
    {
        $ids = $places->pluck('id')->map(fn ($id)=>(int)$id)->flip();
        $byParent = $places->groupBy(fn($place)=>(int)($place->parent_id && $ids->has((int)$place->parent_id) ? $place->parent_id : 0));
        $walk = function ($parentId,$depth) use (&$walk,$byParent) { $rows=[]; foreach($byParent->get((int)$parentId,collect()) as $item) { $rows[]=['place'=>$item,'depth'=>$depth]; $rows=array_merge($rows,$walk($item->id,$depth+1)); } return $rows; };
        return $walk(0,0);
    }
}
