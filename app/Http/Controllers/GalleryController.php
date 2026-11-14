<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterGalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('view') === 'grid') {
            return $this->gridView($request);
        }

        $user = $request->user();
        $world = $this->currentWorld();
        $showMissing = $request->boolean('without_portrait');
        $canEditDescriptions = $world && (
            (int) $world->user_id === (int) $user->id
            || $user->isAdminOrChef()
        );

        $characters = collect();
        if ($world) {
            $characters = Character::query()
                ->where('world_id', $world->id)
                ->whereHas('world', fn ($query) => $query->where('user_id', $user->id))
                ->when(!$showMissing, function ($query) {
                    $query->whereNotNull('image_path')->where('image_path', '!=', '');
                })
                ->get([
                    'id', 'world_id', 'name', 'first_name', 'last_name', 'family_name',
                    'birth_date', 'death_date', 'image_path', 'portrait_description',
                ])
                ->sortBy(function (Character $character) {
                    $family = trim((string) ($character->family_name ?: $character->last_name));
                    return mb_strtolower(($family !== '' ? $family : 'inclassables') . "\0" . $character->display_name);
                }, SORT_STRING)
                ->values();
        }

        $paintings = $characters->map(function (Character $character) use ($canEditDescriptions) {
            $family = trim((string) ($character->family_name ?: $character->last_name));
            $fullName = trim($character->display_name);
            $initials = collect(preg_split('/\s+/u', $fullName))
                ->filter()
                ->take(2)
                ->map(fn ($part) => mb_substr($part, 0, 1))
                ->implode('');

            return [
                'id' => (int) $character->id,
                'name' => $fullName,
                'room' => $family !== '' ? $family : 'Inclassables',
                'birth_year' => optional($character->birth_date)->format('Y'),
                'death_year' => optional($character->death_date)->format('Y'),
                'image_url' => $character->image_path
                    ? route('media.show', ['path' => $character->image_path, 'width' => 600])
                    : null,
                'description' => (string) ($character->portrait_description ?? ''),
                'initials' => $initials ?: '?',
                'sheet_url' => route('manage.characters.show', $character),
                'update_url' => route('manage.characters.update', $character),
                'can_edit' => (bool) $canEditDescriptions,
            ];
        })->values();

        $requestedId = (int) $request->query('p', 0);
        $activeIndex = $paintings->search(fn (array $painting) => $painting['id'] === $requestedId);
        if ($activeIndex === false) {
            $activeIndex = 0;
        }

        $visiblePaintings = $paintings->count() > 30
            ? $paintings->slice(max(0, $activeIndex - 3), min(7, $paintings->count() - max(0, $activeIndex - 3)))->values()
            : $paintings;

        return view('manage.gallery.museum', [
            'activeWorld' => $world,
            'paintings' => $paintings,
            'visiblePaintings' => $visiblePaintings,
            'activeIndex' => $activeIndex,
            'activeId' => $paintings->get($activeIndex)['id'] ?? null,
            'showMissing' => $showMissing,
        ]);
    }

    private function gridView(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $source = (string) $request->query('source', 'all');
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
            ->whereHas('character')
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

