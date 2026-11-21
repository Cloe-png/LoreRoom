<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\CharacterGalleryImage;
use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CharacterGalleryController extends Controller
{
    public function show(Request $request, Character $character)
    {
        $this->ensureCurrentWorld($character);
        $images = $character->galleryImages()->paginate(24)->withQueryString();
        $mentionCount = $character->galleryImages()->where('character_id', '!=', $character->id)->count();
        $canManage = $this->canManage($request, $character);
        $characters = Character::where('world_id', $character->world_id)->where('id', '!=', $character->id)->orderBy('first_name')->orderBy('name')->get(['id', 'name', 'first_name', 'last_name']);
        $images->getCollection()->transform(fn ($image) => $this->present($image, $character));

        return view('manage.gallery.character', compact('character', 'images', 'mentionCount', 'canManage', 'characters'));
    }

    public function store(Request $request, Character $character)
    {
        $this->ensureCurrentWorld($character);
        abort_unless($this->canManage($request, $character), 403);
        $maxImages = method_exists($request->user(), 'quota') ? (int) $request->user()->quota('gallery_images') : 0;
        abort_if($maxImages > 0 && $character->ownedGalleryImages()->count() >= $maxImages, 422, 'Le quota d’images de cette galerie est atteint.');
        $maxKb = method_exists($request->user(), 'quota') ? (int) $request->user()->quota('image_upload_kb') : 10240;
        $data = $request->validate([
            'title' => ['required', 'string', 'max:60'], 'description' => ['nullable', 'string', 'max:280'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:' . max(1024, $maxKb)],
            'mentions' => ['nullable', 'array'], 'mentions.*' => ['integer', 'distinct', 'exists:characters,id'],
        ]);
        $mentionIds = $this->validateMentionIds($data['mentions'] ?? [], $character);
        $paths = $this->storeVariants($request);
        $image = CharacterGalleryImage::create([
            'world_id' => $character->world_id, 'character_id' => $character->id,
            'image_path' => $paths['thumb'], 'display_path' => $paths['display'],
            'title' => $data['title'], 'caption' => $data['title'], 'description' => $this->clean($data['description'] ?? ''),
            'sort_order' => 0, 'position' => 0,
        ]);
        $image->mentions()->sync($mentionIds);
        UserLog::logAction($request->user()->id, 'gallery_image_added', 'manage.character-gallery.store', (string) $image->id);

        return response()->json(['image' => $this->present($image->load(['owner', 'mentions']), $character)], 201);
    }

    public function update(Request $request, Character $character, CharacterGalleryImage $image)
    {
        $this->ensureImage($character, $image);
        abort_unless($this->canManage($request, $character), 403);
        $data = $request->validate(['description' => ['nullable', 'string', 'max:280']]);
        $image->description = $this->clean($data['description'] ?? '');
        $image->save();
        UserLog::logAction($request->user()->id, 'gallery_image_description_updated', 'manage.character-gallery.update', (string) $image->id);
        return response()->json(['description' => $image->description]);
    }

    public function mention(Request $request, Character $character, CharacterGalleryImage $image)
    {
        $this->ensureImage($character, $image);
        abort_unless($this->canManage($request, $character), 403);
        $data = $request->validate(['character_id' => ['required', 'integer', 'exists:characters,id']]);
        $ids = $this->validateMentionIds([(int) $data['character_id']], $image->character);
        $image->mentions()->syncWithoutDetaching($ids);
        UserLog::logAction($request->user()->id, 'gallery_image_mentioned', 'manage.character-gallery.mention', (string) $image->id);
        return response()->json(['mentions' => $image->fresh()->mentions()->get(['characters.id', 'name', 'first_name', 'last_name'])]);
    }

    public function unmention(Request $request, Character $character, CharacterGalleryImage $image, Character $mentioned)
    {
        $this->ensureImage($character, $image);
        abort_unless($this->canManage($request, $character), 403);
        abort_if((int) $image->character_id === (int) $mentioned->id, 422);
        $image->mentions()->detach($mentioned->id);
        UserLog::logAction($request->user()->id, 'gallery_image_unmentioned', 'manage.character-gallery.unmention', (string) $image->id);
        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, Character $character, CharacterGalleryImage $image)
    {
        $this->ensureImage($character, $image);
        abort_unless($this->canManage($request, $character), 403);
        $image->delete();
        Storage::disk('public')->delete(array_filter([$image->image_path, $image->display_path]));
        UserLog::logAction($request->user()->id, 'gallery_image_deleted', 'manage.character-gallery.destroy', (string) $image->id);
        return response()->json(['ok' => true]);
    }

    private function ensureCurrentWorld(Character $character): void
    {
        abort_unless((int) $character->world_id === (int) session('selected_world_id'), 403);
        abort_unless($character->world()->where('user_id', auth()->id())->exists(), 403);
    }

    private function ensureImage(Character $character, CharacterGalleryImage $image): void
    {
        abort_unless((int) $image->world_id === (int) $character->world_id, 404);
        abort_unless($character->galleryImages()->whereKey($image->id)->exists(), 404);
    }

    private function canManage(Request $request, Character $character): bool
    {
        return (int) $character->world()->value('user_id') === (int) $request->user()->id || $request->user()->isAdminOrChef();
    }

    private function validateMentionIds(array $ids, Character $owner): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        abort_if(in_array((int) $owner->id, $ids, true), 422, 'Le propriétaire de la photo ne peut pas être mentionné.');
        if ($ids) {
            $valid = Character::where('world_id', $owner->world_id)->whereIn('id', $ids)->pluck('id')->map(fn ($id) => (int) $id)->all();
            abort_unless(count($valid) === count($ids), 422, 'Les personnages mentionnés doivent appartenir au même monde.');
        }
        return $ids;
    }

    private function clean(?string $value): string
    {
        return trim(strip_tags((string) $value));
    }

    private function storeVariants(Request $request): array
    {
        $file = $request->file('image');
        $base = 'character-galleries/' . now()->format('Y/m') . '/' . Str::uuid();
        $original = $file->storeAs(dirname($base), basename($base) . '.' . $file->extension(), 'public');
        $display = $this->resizeWebp($file->getRealPath(), $base . '-display.webp', 1600) ?: $original;
        $thumb = $this->resizeWebp($file->getRealPath(), $base . '-thumb.webp', 600) ?: $display;
        if ($original !== $display && $original !== $thumb) Storage::disk('public')->delete($original);
        return ['thumb' => $thumb, 'display' => $display];
    }

    private function resizeWebp(string $sourcePath, string $targetPath, int $maxWidth): ?string
    {
        if (!function_exists('imagecreatefromstring') || !function_exists('imagewebp')) return null;
        $source = @imagecreatefromstring(file_get_contents($sourcePath));
        if (!$source) return null;
        $width = imagesx($source); $height = imagesy($source);
        $targetWidth = min($maxWidth, $width); $targetHeight = max(1, (int) round($height * $targetWidth / $width));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false); imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        $saved = imagewebp($target, Storage::disk('public')->path($targetPath), 82);
        imagedestroy($source); imagedestroy($target);
        if (!$saved) return null;
        return $targetPath;
    }

    private function present(CharacterGalleryImage $image, Character $current): array
    {
        return [
            'id' => (int) $image->id, 'title' => $image->title ?: ($image->caption ?: 'Photo'),
            'description' => (string) $image->description, 'url' => route('media.show', ['path' => $image->display_path ?: $image->image_path]),
            'thumb_url' => route('media.show', ['path' => $image->image_path, 'width' => 600]),
            'alt' => ($image->title ?: 'Photo') . ', photo de ' . optional($image->owner)->display_name,
            'owner' => optional($image->owner)->display_name, 'owner_url' => (int) $image->character_id === (int) $current->id ? null : route('manage.character-gallery.show', $image->character_id),
            'owner_id' => (int) $image->character_id,
            'is_mention' => (int) $image->character_id !== (int) $current->id,
            'mentions' => $image->mentions->map(fn ($mention) => ['id' => $mention->id, 'name' => $mention->display_name, 'url' => route('manage.character-gallery.show', $mention)]),
            'can_manage' => $this->canManage(request(), $current),
            'update_url' => route('manage.character-gallery.update', [$current, $image]),
            'mention_url' => route('manage.character-gallery.mention', [$current, $image]),
            'delete_url' => route('manage.character-gallery.destroy', [$current, $image]),
        ];
    }
}
