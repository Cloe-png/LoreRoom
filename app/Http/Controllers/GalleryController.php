<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterGalleryImage;
use Illuminate\Support\Collection;

class GalleryController extends Controller
{
    public function index()
    {
        $q = trim((string) request('q', ''));
        $source = (string) request('source', 'all');
        if (!in_array($source, ['all', 'portrait', 'gallery'], true)) {
            $source = 'all';
        }

        $portraits = Character::query()
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->get(['id', 'name', 'first_name', 'last_name', 'image_path', 'preferred_color', 'created_at'])
            ->map(function (Character $character) {
                return [
                    'uid' => 'portrait-' . $character->id,
                    'source' => 'portrait',
                    'source_label' => 'Portrait',
                    'image_path' => $character->image_path,
                    'caption' => null,
                    'character_id' => $character->id,
                    'character_name' => $character->display_name,
                    'preferred_color' => $character->preferred_color,
                    'created_at' => $character->created_at,
                ];
            });

        $galleryImages = CharacterGalleryImage::query()
            ->with('character:id,name,first_name,last_name,preferred_color')
            ->get(['id', 'character_id', 'image_path', 'caption', 'created_at'])
            ->map(function (CharacterGalleryImage $image) {
                return [
                    'uid' => 'gallery-' . $image->id,
                    'source' => 'gallery',
                    'source_label' => 'Galerie',
                    'image_path' => $image->image_path,
                    'caption' => $image->caption,
                    'character_id' => $image->character_id,
                    'character_name' => optional($image->character)->display_name ?: 'Personnage inconnu',
                    'preferred_color' => optional($image->character)->preferred_color,
                    'created_at' => $image->created_at,
                ];
            });

        $images = $this->mergeAndFilter($portraits, $galleryImages, $q, $source);

        return view('manage.gallery.index', [
            'images' => $images,
            'q' => $q,
            'source' => $source,
            'totalCount' => $images->count(),
        ]);
    }

    private function mergeAndFilter(Collection $portraits, Collection $galleryImages, string $q, string $source): Collection
    {
        $images = $portraits->concat($galleryImages);

        if ($source !== 'all') {
            $images = $images->where('source', $source);
        }

        if ($q !== '') {
            $needle = mb_strtolower($q);
            $images = $images->filter(function (array $item) use ($needle) {
                $name = mb_strtolower((string) ($item['character_name'] ?? ''));
                $caption = mb_strtolower((string) ($item['caption'] ?? ''));
                $label = mb_strtolower((string) ($item['source_label'] ?? ''));

                return str_contains($name, $needle)
                    || str_contains($caption, $needle)
                    || str_contains($label, $needle);
            });
        }

        return $images
            ->sortByDesc(fn (array $item) => $item['created_at'])
            ->values();
    }
}

