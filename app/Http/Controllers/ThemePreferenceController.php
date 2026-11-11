<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemePreferenceController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate(['theme' => 'required|in:dark,light,auto']);
        $request->user()->update(['theme' => $validated['theme']]);

        return response()->json(['theme' => $validated['theme']]);
    }
}
