<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSessionActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $now = Carbon::now();
        $lifetimeMinutes = (int) config('session.lifetime', 120);
        $lastActivity = $request->session()->get('last_activity_at');

        if ($lastActivity) {
            $last = $lastActivity instanceof Carbon ? $lastActivity : Carbon::parse($lastActivity);
            if ($last->diffInMinutes($now) >= $lifetimeMinutes) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'auth' => 'Session expirée pour inactivité. Reconnecte-toi.',
                ]);
            }
        }

        $request->session()->put('last_activity_at', $now->toDateTimeString());

        return $next($request);
    }
}
