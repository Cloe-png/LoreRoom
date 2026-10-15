<?php

use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CharacterRelationController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ChronicleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\FactionController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserAnalyticsController;
use App\Http\Controllers\UserAdminController;
use App\Http\Controllers\WorldController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GenealogyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LoreController;
use App\Http\Controllers\SpeciesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('start');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/portals', function () {
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

Route::middleware(['auth', 'temp.token', 'session.activity', 'world.selected'])->group(function () {
    Route::get('/manage', [ManageController::class, 'index'])->name('manage.index');
    Route::get('manage/account', [AccountSettingsController::class, 'edit'])->name('manage.account.edit');
    Route::put('manage/account/password', [AccountSettingsController::class, 'updatePassword'])->name('manage.account.password');
    Route::put('manage/account/locale', [AccountSettingsController::class, 'updateLocale'])->name('manage.account.locale');
    Route::put('manage/account/roles', [AccountSettingsController::class, 'updateRoles'])->name('manage.account.roles');
    Route::get('manage/account/export-world', [AccountSettingsController::class, 'exportWorld'])->name('manage.account.export-world');
    Route::post('manage/account/import-world', [AccountSettingsController::class, 'importWorld'])->name('manage.account.import-world');
    Route::get('manage/characters/{character}/export-pdf', [CharacterController::class, 'exportPdf'])->name('manage.characters.export-pdf');
    Route::post('manage/worlds/{world}/switch', [WorldController::class, 'switch'])->name('manage.worlds.switch');
    Route::resource('manage/worlds', WorldController::class)->names('manage.worlds');
    Route::resource('manage/characters', CharacterController::class)->names('manage.characters');
    Route::middleware('module.access:chapters')->group(function () {
        Route::resource('manage/chapters', ChapterController::class)->names('manage.chapters');
    });
    Route::post('manage/places/{place}/duplicate', [PlaceController::class, 'duplicate'])->name('manage.places.duplicate');
    Route::resource('manage/places', PlaceController::class)->names('manage.places');
    Route::get('manage/chronicles/global', [ChronicleController::class, 'globalTimeline'])->name('manage.chronicles.global');
    Route::get('manage/chronicles/characters/{character}', [ChronicleController::class, 'characterTimeline'])->name('manage.chronicles.character');
    Route::resource('manage/chronicles', ChronicleController::class)->names('manage.chronicles');
    Route::resource('manage/relations', CharacterRelationController::class)->names('manage.relations');

    Route::middleware('module.access:jobs')->group(function () {
        Route::resource('manage/jobs', JobController::class)->names('manage.jobs');
    });

    Route::middleware('module.access:lore')->group(function () {
        Route::resource('manage/lore', LoreController::class)->names('manage.lore');
        Route::resource('manage/species', SpeciesController::class)->names('manage.species');
    });

    Route::middleware('module.access:factions')->group(function () {
        Route::resource('manage/factions', FactionController::class)->names('manage.factions');
    });

    Route::middleware('module.access:gallery')->group(function () {
        Route::get('manage/galerie', [GalleryController::class, 'index'])->name('manage.gallery.index');
    });

    Route::middleware('module.access:genealogy')->group(function () {
        Route::get('manage/arbre-genealogique', [GenealogyController::class, 'index'])->name('manage.genealogy.index');
    });

    Route::middleware('module.access:suggestions')->group(function () {
        Route::get('manage/suggestions', [SuggestionController::class, 'index'])->name('manage.suggestions.index');
    });

    Route::middleware('module.access:trash')->group(function () {
        Route::get('manage/trash', [TrashController::class, 'index'])->name('manage.trash.index');
        Route::post('manage/trash/empty', [TrashController::class, 'empty'])->name('manage.trash.empty');
        Route::post('manage/trash/{type}/{id}/restore', [TrashController::class, 'restore'])->name('manage.trash.restore');
        Route::delete('manage/trash/{type}/{id}', [TrashController::class, 'destroy'])->name('manage.trash.destroy');
    });

    Route::middleware('module.access:analytics')->group(function () {
        Route::get('manage/analytics', [UserAnalyticsController::class, 'index'])->name('manage.analytics.index');
    });

    Route::middleware('module.access:users')->group(function () {
        Route::get('manage/users', [UserAdminController::class, 'index'])->name('manage.users.index');
        Route::put('manage/users/{user}', [UserAdminController::class, 'update'])->name('manage.users.update');
        Route::put('manage/users/{user}/password', [UserAdminController::class, 'updatePassword'])->name('manage.users.password');
        Route::delete('manage/users/{user}', [UserAdminController::class, 'destroy'])->name('manage.users.destroy');
    });
});

Route::get('/story', function () {
    return view('story');
});
