<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function complete(Request $request): JsonResponse
    {
        $request->user()->forceFill(['tour_completed_at' => now()])->save();

        $result = ['completed' => true];

        if ($request->boolean('create_world')) {
            $request->session()->flash('success', __('tour.create_world_hint'));
            $result['redirect'] = route('manage.worlds.create');
        } elseif ($request->boolean('choose_world')) {
            $result['redirect'] = route('manage.worlds.index');
        }

        return response()->json($result);
    }

    public function restart(Request $request): JsonResponse
    {
        $request->user()->forceFill(['tour_completed_at' => null])->save();

        return response()->json(['completed' => false]);
    }
}
