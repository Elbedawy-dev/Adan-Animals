<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetFilamentLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('localization.supported_locales', ['en', 'ar']);
        $locale = session('filament_locale', config('app.locale', 'en'));

        if (! in_array($locale, $supported, true)) {
            $locale = 'en';
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
