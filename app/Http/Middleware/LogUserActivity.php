<?php

namespace App\Http\Middleware;

use App\Models\UserLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$request->isMethod('get') || !Auth::check()) {
            return $response;
        }

        $route = $request->route();
        if (!$route) {
            return $response;
        }

        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $routeName = (string) $route->getName();
        if ($routeName !== 'manage.index') {
            $request->session()->put('manage_last_open', [
                'route_name' => $routeName,
                'url' => $request->fullUrl(),
                'label' => $this->resolvePageLabel($request),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        UserLog::logAction(
            (int) Auth::id(),
            'page_consulte',
            $route->getName(),
            $route->uri()
        );

        return $response;
    }

    private function resolvePageLabel(Request $request): string
    {
        $route = $request->route();
        if (!$route) {
            return 'Dernière page';
        }

        foreach ($route->parameters() as $parameter) {
            if (!is_object($parameter)) {
                continue;
            }

            foreach (['display_name', 'title', 'name'] as $attribute) {
                if (isset($parameter->{$attribute}) && trim((string) $parameter->{$attribute}) !== '') {
                    return trim((string) $parameter->{$attribute});
                }
            }
        }

        switch ((string) $route->getName()) {
            case 'manage.characters.index':
                return 'Personnages';
            case 'manage.characters.create':
                return 'Nouveau personnage';
            case 'manage.chronicles.index':
                return 'Chroniques';
            case 'manage.chronicles.create':
                return 'Nouvelle chronique';
            case 'manage.places.index':
                return 'Lieux';
            case 'manage.places.create':
                return 'Nouveau lieu';
            case 'manage.worlds.index':
                return 'Mondes';
            case 'manage.worlds.create':
                return 'Nouveau monde';
            case 'manage.factions.index':
                return 'Factions';
            case 'manage.genealogy.index':
                return 'Arbre généalogique';
            case 'manage.gallery.index':
                return 'Galerie';
            case 'manage.analytics.index':
                return 'Analytics';
            case 'manage.trash.index':
                return 'Corbeille';
            default:
                return 'Dernière page';
        }
    }
}
