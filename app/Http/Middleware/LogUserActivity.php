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

        UserLog::logAction(
            (int) Auth::id(),
            'page_consulte',
            $route->getName(),
            $route->uri()
        );

        return $response;
    }
}
