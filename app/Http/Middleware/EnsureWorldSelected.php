<?php

namespace App\Http\Middleware;

use App\Models\World;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EnsureWorldSelected
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $worlds = $user->worlds()
            ->orderBy('name')
            ->get(['worlds.id', 'worlds.name', 'worlds.slug', 'worlds.geography_type']);

        $allowedWorldIds = $worlds->pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowedMap = array_flip($allowedWorldIds);

        if ($worlds->isEmpty()) {
            if (!$request->routeIs('manage.worlds.*') && !$request->routeIs('manage.users.*')) {
                return redirect()
                    ->route('manage.worlds.index')
                    ->with('success', 'Bienvenue. Crée ton premier monde pour commencer.');
            }

            $request->session()->forget('selected_world_id');
            if ((int) ($user->current_world_id ?? 0) !== 0) {
                $user->forceFill(['current_world_id' => null])->save();
            }

            View::share('activeWorld', null);
            View::share('availableWorlds', collect());

            return $next($request);
        }

        $selectedWorldId = (int) ($user->current_world_id ?? 0);
        if ($selectedWorldId <= 0 || !isset($allowedMap[$selectedWorldId])) {
            $selectedWorldId = (int) ($worlds->first()->id ?? 0);
        }

        if ((int) ($user->current_world_id ?? 0) !== $selectedWorldId) {
            $user->forceFill(['current_world_id' => $selectedWorldId > 0 ? $selectedWorldId : null])->save();
        }

        if ($selectedWorldId > 0) {
            $request->session()->put('selected_world_id', $selectedWorldId);
            $activeWorld = World::query()->find($selectedWorldId);
            if ($activeWorld) {
                $request->attributes->set('active_world', $activeWorld);
                View::share('activeWorld', $activeWorld);
            }
        } else {
            $request->session()->forget('selected_world_id');
            View::share('activeWorld', null);
        }

        View::share('availableWorlds', $worlds);

        return $next($request);
    }
}
