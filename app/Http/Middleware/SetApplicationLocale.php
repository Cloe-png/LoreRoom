<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class SetApplicationLocale
{
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale('fr');
        Carbon::setLocale('fr');

        return $next($request);
    }
}
