<?php

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

Route::get('/worlds', [WorldApiController::class, 'index']);
Route::get('/worlds/{world}/characters', [WorldApiController::class, 'characters']);
Route::get('/worlds/{world}/stats', [WorldApiController::class, 'stats']);
