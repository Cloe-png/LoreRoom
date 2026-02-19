<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTemporaryLoginToken
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $sessionToken = (string) $request->session()->get('login_temp_token', '');
        $sessionHash = $sessionToken !== '' ? hash('sha256', $sessionToken) : '';
        $storedHash = (string) ($user->login_token_hash ?? '');
        $expiresAt = $user->login_token_expires_at;

        $isValid = $sessionHash !== ''
            && $storedHash !== ''
            && hash_equals($storedHash, $sessionHash)
            && $expiresAt
            && $expiresAt->isFuture();

        if (!$isValid) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'auth' => 'Session expirée. Reconnecte-toi.',
            ]);
        }

        return $next($request);
    }
}
