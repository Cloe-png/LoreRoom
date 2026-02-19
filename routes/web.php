<?php

use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CharacterRelationController;
use App\Http\Controllers\ChronicleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImaginaryMapController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\WorldController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GenealogyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('start');
});

Route::get('/portals', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', function () {
    return redirect()->route('login');
});
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
Route::get('/register', function () {
    return redirect()->route('register');
});
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/media/{path}', [MediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

Route::middleware('auth')->group(function () {
    Route::get('/manage', [ManageController::class, 'index'])->name('manage.index');
    Route::get('manage/galerie', [GalleryController::class, 'index'])->name('manage.gallery.index');
    Route::get('manage/arbre-genealogique', [GenealogyController::class, 'index'])->name('manage.genealogy.index');
    Route::get('manage/characters/{character}/export-pdf', [CharacterController::class, 'exportPdf'])->name('manage.characters.export-pdf');
    Route::resource('manage/worlds', WorldController::class)->names('manage.worlds');
    Route::resource('manage/characters', CharacterController::class)->names('manage.characters');
    Route::resource('manage/places', PlaceController::class)->names('manage.places');
    Route::get('manage/chronicles/global', [ChronicleController::class, 'globalTimeline'])->name('manage.chronicles.global');
    Route::get('manage/chronicles/characters/{character}', [ChronicleController::class, 'characterTimeline'])->name('manage.chronicles.character');
    Route::resource('manage/chronicles', ChronicleController::class)->names('manage.chronicles');
    Route::resource('manage/maps', ImaginaryMapController::class)->names('manage.maps');
    Route::resource('manage/relations', CharacterRelationController::class)->names('manage.relations');
});

Route::get('/story', function () {
    return view('story');
});
