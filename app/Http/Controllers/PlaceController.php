<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Support\UploadSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PlaceController extends Controller
{
    private const PLACE_TYPES = [
        'ville',
        'village',
        'capitale',
        'quartier',
        'region',
        'province',
        'ile',
        'continent',
        'montagne',
        'foret',
        'desert',
        'ocean',
        'ruines',
        'donjon',
        'temple',
        'royaume',
    ];

    public function index()
    {
        $places = Place::with('world')->latest()->paginate(10);

        return view('manage.places.index', compact('places'));
    }

    public function create()
    {
        $defaultWorld = $this->currentWorld();

        return view('manage.places.create', [
            'defaultWorld' => $defaultWorld,
            'placeTypeOptions' => self::PLACE_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $defaultWorldId = $this->requireCurrentWorldId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['nullable', Rule::in(self::PLACE_TYPES)],
            'region' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'image' => UploadSecurity::imageRules(4096),
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => UploadSecurity::imageRules(4096),
            'gallery_captions' => ['nullable', 'array'],
            'gallery_captions.*' => ['nullable', 'string', 'max:255'],
        ]);
        $data['world_id'] = $defaultWorldId;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('places', 'public');
        }

        unset($data['gallery_images'], $data['gallery_captions']);

        $place = Place::create($data);
        $this->addPlaceGalleryImages($place, $request->file('gallery_images', []), $request->input('gallery_captions', []));

        return redirect()->route('manage.places.index')->with('success', 'Lieu créé.');
    }

    public function show(Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $place->load([
            'world',
            'galleryImages',
            'eventChronicles' => fn ($q) => $q->latest()->take(8),
            'birthCharacters' => fn ($q) => $q->orderBy('name')->take(12),
            'residentCharacters' => fn ($q) => $q->orderBy('name')->take(12),
        ]);

        return view('manage.places.show', [
            'place' => $place,
            'eventChroniclesCount' => $place->eventChronicles()->count(),
            'birthCharactersCount' => $place->birthCharacters()->count(),
            'residentCharactersCount' => $place->residentCharacters()->count(),
        ]);
    }

    public function edit(Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $defaultWorld = $this->currentWorld();

        return view('manage.places.edit', [
            'place' => $place,
            'defaultWorld' => $defaultWorld,
            'placeTypeOptions' => self::PLACE_TYPES,
        ]);
    }

    public function update(Request $request, Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);
        $defaultWorldId = $this->requireCurrentWorldId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['nullable', Rule::in(self::PLACE_TYPES)],
            'region' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'image' => UploadSecurity::imageRules(4096),
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => UploadSecurity::imageRules(4096),
            'gallery_captions' => ['nullable', 'array'],
            'gallery_captions.*' => ['nullable', 'string', 'max:255'],
            'remove_gallery_ids' => ['nullable', 'array'],
            'remove_gallery_ids.*' => ['nullable', 'integer'],
        ]);
        $data['world_id'] = $defaultWorldId;

        if ($request->hasFile('image')) {
            if ($place->image_path) {
                Storage::disk('public')->delete($place->image_path);
            }
            $data['image_path'] = $request->file('image')->store('places', 'public');
        }

        $removeGalleryIds = collect($data['remove_gallery_ids'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        unset($data['gallery_images'], $data['gallery_captions'], $data['remove_gallery_ids']);

        $place->update($data);
        $this->removePlaceGalleryImages($place, $removeGalleryIds);
        $this->addPlaceGalleryImages($place, $request->file('gallery_images', []), $request->input('gallery_captions', []));

        return redirect()->route('manage.places.index')->with('success', 'Lieu mis à jour.');
    }

    public function destroy(Place $place)
    {
        $this->abortIfOutsideCurrentWorld((int) $place->world_id);

        $place->delete();

        return redirect()->route('manage.places.index')->with('success', 'Lieu supprimé.');
    }

    private function removePlaceGalleryImages(Place $place, array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $images = $place->galleryImages()->whereIn('id', $ids)->get();
        foreach ($images as $image) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }
    }

    private function addPlaceGalleryImages(Place $place, array $uploadedFiles, array $captions): void
    {
        if (empty($uploadedFiles)) {
            return;
        }

        $startOrder = (int) ($place->galleryImages()->max('sort_order') ?? 0);

        foreach ($uploadedFiles as $index => $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store('places/gallery', 'public');
            $startOrder++;

            $place->galleryImages()->create([
                'image_path' => $path,
                'caption' => trim((string) ($captions[$index] ?? '')) ?: null,
                'sort_order' => $startOrder,
            ]);
        }
    }
}
