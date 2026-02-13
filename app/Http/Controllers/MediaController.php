<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function show(string $path)
    {
        $cleanPath = ltrim($path, '/');

        if (!Storage::disk('public')->exists($cleanPath)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($cleanPath));
    }
}

