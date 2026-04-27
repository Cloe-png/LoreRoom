<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\World;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class WorldApiController extends Controller
{
    public function index(): JsonResponse
    {
        $worlds = World::query()
            ->select('id', 'name', 'slug', 'status', 'summary')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $worlds,
        ]);
    }

    public function characters(World $world): JsonResponse
    {
        $characters = $world->characters()
            ->select('id', 'world_id', 'name', 'role', 'status', 'birth_date', 'death_date')
            ->orderBy('name')
            ->get();

        return response()->json([
            'world' => [
                'id' => $world->id,
                'name' => $world->name,
            ],
            'data' => $characters,
        ]);
    }

    public function stats(World $world): JsonResponse
    {
        $characterCount = DB::selectOne(
            'SELECT fn_world_character_count(?) AS total',
            [$world->id]
        );

        $dashboard = DB::selectOne('CALL sp_world_dashboard(?)', [$world->id]);

        return response()->json([
            'world' => [
                'id' => $world->id,
                'name' => $world->name,
            ],
            'function_result' => [
                'character_count' => (int) ($characterCount->total ?? 0),
            ],
            'procedure_result' => $dashboard,
        ]);
    }
};
