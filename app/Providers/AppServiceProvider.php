<?php

namespace App\Providers;

use App\Models\Faction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        View::composer('manage.layout', function ($view) {
            if (app()->runningInConsole()) {
                return;
            }

            $request = request();
            if (!$request->is('manage*')) {
                $view->with('sidebarFactions', collect());
                return;
            }

            $worldId = (int) $request->session()->get('selected_world_id', 0);
            if ($worldId <= 0) {
                $view->with('sidebarFactions', collect());
                return;
            }

            if (!Schema::hasTable('factions')) {
                $view->with('sidebarFactions', collect());
                return;
            }

            $factions = Faction::orderBy('name')->get(['id', 'name']);
            $view->with('sidebarFactions', $factions);
        });
    }
}
