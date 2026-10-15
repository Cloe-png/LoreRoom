<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SetApplicationLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        $locale = '';

        if ($request->hasSession()) {
            $locale = (string) $request->session()->get('locale', '');
        }

        if ($locale === '' && $request->user() && in_array((string) ($request->user()->preferred_locale ?? ''), $supportedLocales, true)) {
            $locale = (string) $request->user()->preferred_locale;

            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
        }

        if (!in_array($locale, $supportedLocales, true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
