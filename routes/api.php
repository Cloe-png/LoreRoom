<?php

use App\Http\Controllers\Api\CharacterApiController;
use App\Http\Controllers\Api\CharacterRelationApiController;
use App\Http\Controllers\Api\ChronicleApiController;
use App\Http\Controllers\Api\FactionApiController;
use App\Http\Controllers\Api\JobApiController;
use App\Http\Controllers\Api\PlaceApiController;
use App\Http\Controllers\Api\SpeciesApiController;
use App\Http\Controllers\Api\WorldApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('worlds', WorldApiController::class);
Route::get('/worlds/{world}/characters', [WorldApiController::class, 'characters']);
Route::get('/worlds/{world}/stats', [WorldApiController::class, 'stats']);
Route::apiResource('characters', CharacterApiController::class);
Route::apiResource('places', PlaceApiController::class);
Route::apiResource('chronicles', ChronicleApiController::class);
Route::apiResource('factions', FactionApiController::class);
Route::apiResource('jobs', JobApiController::class);
Route::apiResource('species', SpeciesApiController::class);
Route::apiResource('relations', CharacterRelationApiController::class);
