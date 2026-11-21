<?php

namespace App\Http\Middleware;

use App\Models\Character;
use App\Models\CharacterRelation;
use App\Models\Place;
use App\Models\World;
use Closure;
use Illuminate\Http\Request;

class EnsureApiOwnership
{
    public function handle(Request $request, Closure $next)
    {
        $userId = (int) $request->user()->getAuthIdentifier();

        if ($request->filled('user_id') && (int) $request->input('user_id') !== $userId) {
            abort(404);
        }

        if ($request->filled('world_id') && ! World::whereKey($request->input('world_id'))->where('user_id', $userId)->exists()) {
            abort(404);
        }

        foreach ($request->route()->parameters() as $parameter) {
            if ($parameter instanceof World) {
                abort_unless((int) $parameter->user_id === $userId, 404);
            } elseif ($parameter instanceof CharacterRelation) {
                abort_unless((int) $parameter->fromCharacter?->world?->user_id === $userId, 404);
            } elseif ($parameter instanceof Character) {
                abort_unless((int) $parameter->world?->user_id === $userId, 404);
            } elseif (is_object($parameter) && method_exists($parameter, 'world')) {
                abort_unless((int) $parameter->world?->user_id === $userId, 404);
            }
        }

        $routeWorld = $request->route('world');
        $routeCharacter = $request->route('character');
        $worldId = $request->input('world_id')
            ?: ($routeWorld instanceof World ? $routeWorld->id : null)
            ?: ($routeCharacter instanceof Character ? $routeCharacter->world_id : null);
        if (! $worldId) {
            foreach ($request->route()->parameters() as $parameter) {
                if (is_object($parameter) && method_exists($parameter, 'getAttribute') && $parameter->getAttribute('world_id')) {
                    $worldId = $parameter->getAttribute('world_id');
                    break;
                }
            }
        }
        $relationWorldId = $worldId ?: ($request->filled('from_character_id')
            ? Character::whereKey($request->input('from_character_id'))->value('world_id')
            : null);

        foreach (['from_character_id', 'to_character_id'] as $key) {
            if ($request->filled($key) && ! Character::whereKey($request->input($key))
                ->whereHas('world', fn ($query) => $query->where('user_id', $userId))
                ->when($relationWorldId, fn ($query) => $query->where('world_id', $relationWorldId))->exists()) {
                abort(404);
            }
        }

        foreach (['father_id', 'mother_id', 'spouse_id', 'leader_id', 'co_leader_id', 'founder_id'] as $key) {
            if ($request->filled($key) && ! Character::whereKey($request->input($key))->where('world_id', $worldId)->exists()) {
                abort(404);
            }
        }

        foreach (['birth_place_id', 'residence_place_id', 'event_place_id', 'parent_id'] as $key) {
            if ($request->filled($key) && ! Place::whereKey($request->input($key))->where('world_id', $worldId)->exists()) {
                abort(404);
            }
        }

        return $next($request);
    }
}
