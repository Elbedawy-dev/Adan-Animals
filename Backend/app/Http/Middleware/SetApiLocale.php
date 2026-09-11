<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('localization.supported_locales', ['en', 'ar']);
        $default = config('localization.default_locale', 'en');

        $locale = $request->header('X-Locale')
            ?? $request->header('Accept-Language');

        if (is_string($locale) && $locale !== '') {
            $locale = strtolower(str_replace('_', '-', explode(',', $locale)[0]));
            $locale = substr($locale, 0, 2);
        } else {
            $locale = $default;
        }

        if (! in_array($locale, $supported, true)) {
            $locale = $default;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
