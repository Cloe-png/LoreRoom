<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function show(Request $request, string $path)
    {
        $cleanPath = ltrim($path, '/');

        if ($cleanPath === '' || str_contains($cleanPath, '..') || str_contains($cleanPath, "\\") || str_contains($cleanPath, "\0")) {
            abort(404);
        }

        abort_unless($this->belongsToUser($cleanPath, (int) $request->user()->getAuthIdentifier()), 404);

        if (!Storage::disk('public')->exists($cleanPath)) {
            abort(404);
        }

        $absolutePath = Storage::disk('public')->path($cleanPath);
        $requestedWidth = (int) $request->query('width', 0);
        if ($requestedWidth > 0 && function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            $imageInfo = @getimagesize($absolutePath);
            if ($imageInfo && strpos((string) $imageInfo['mime'], 'image/') === 0) {
                $source = @imagecreatefromstring(file_get_contents($absolutePath));
                if ($source) {
                    $sourceWidth = imagesx($source);
                    $sourceHeight = imagesy($source);
                    $targetWidth = min(max(1, $requestedWidth), 600, $sourceWidth);
                    $targetHeight = max(1, (int) round($sourceHeight * ($targetWidth / $sourceWidth)));
                    $resized = imagecreatetruecolor($targetWidth, $targetHeight);
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

                    ob_start();
                    $encoded = imagewebp($resized, null, 82);
                    $webp = ob_get_clean();
                    imagedestroy($resized);
                    imagedestroy($source);

                    if ($encoded && $webp !== false) {
                        return response($webp, 200, [
                            'Content-Type' => 'image/webp',
                            'Cache-Control' => 'private, max-age=86400',
                            'X-Content-Type-Options' => 'nosniff',
                        ]);
                    }
                }
            }
        }

        return response()->file($absolutePath, [
            'Cache-Control' => 'private, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function belongsToUser(string $path, int $userId): bool
    {
        return DB::table('characters')->join('worlds', 'worlds.id', '=', 'characters.world_id')
                ->where('worlds.user_id', $userId)
                ->where(fn ($query) => $query->where('characters.image_path', $path)->orWhere('characters.voice_audio_path', $path))->exists()
            || DB::table('character_gallery_images')->join('characters', 'characters.id', '=', 'character_gallery_images.character_id')
                ->join('worlds', 'worlds.id', '=', 'characters.world_id')->where('worlds.user_id', $userId)
                ->where(fn ($query) => $query->where('character_gallery_images.image_path', $path)->orWhere('character_gallery_images.display_path', $path))->exists()
            || DB::table('character_outfit_images')->join('characters', 'characters.id', '=', 'character_outfit_images.character_id')
                ->join('worlds', 'worlds.id', '=', 'characters.world_id')->where('worlds.user_id', $userId)->where('character_outfit_images.image_path', $path)->exists()
            || DB::table('places')->join('worlds', 'worlds.id', '=', 'places.world_id')
                ->where('worlds.user_id', $userId)->where('places.image_path', $path)->exists()
            || DB::table('place_gallery_images')->join('places', 'places.id', '=', 'place_gallery_images.place_id')
                ->join('worlds', 'worlds.id', '=', 'places.world_id')->where('worlds.user_id', $userId)->where('place_gallery_images.image_path', $path)->exists()
            || DB::table('factions')->join('worlds', 'worlds.id', '=', 'factions.world_id')
                ->where('worlds.user_id', $userId)->where('factions.logo_path', $path)->exists();
    }
}

