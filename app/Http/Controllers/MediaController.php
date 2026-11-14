<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function show(Request $request, string $path)
    {
        $cleanPath = ltrim($path, '/');

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
                            'Cache-Control' => 'public, max-age=86400',
                        ]);
                    }
                }
            }
        }

        return response()->file($absolutePath);
    }
}

