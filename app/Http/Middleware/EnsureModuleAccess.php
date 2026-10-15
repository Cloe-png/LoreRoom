<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureModuleAccess
{
    public function handle(Request $request, Closure $next, string $module)
    {
        $user = $request->user();

        if ($user && $user->canAccessModule($module)) {
            return $next($request);
        }

        throw new AccessDeniedHttpException(__('app.messages.module_access_denied'));
    }
}
