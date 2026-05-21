<?php

namespace App\Http\Controllers;

use App\Support\UploadSecurity;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function show(string $path)
    {
        $cleanPath = ltrim($path, '/');

        if (!UploadSecurity::hasAllowedExtension($cleanPath) || !Storage::disk('public')->exists($cleanPath)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($cleanPath));
    }
}

