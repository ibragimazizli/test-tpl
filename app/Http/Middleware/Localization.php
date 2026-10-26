<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        // Fallback for API requests hitting un-prefixed URLs: Infer locale from the Referer header
        if (!$locale && ($request->is('callService/*') || $request->is('get-token'))) {
            $referer = $request->headers->get('referer');
            if ($referer) {
                $path = parse_url($referer, PHP_URL_PATH);
                if ($path) {
                    $segments = explode('/', trim($path, '/'));
                    $locales = ['az', 'en', 'ru', 'ka', 'fa', 'tr'];
                    if (isset($segments[0]) && in_array($segments[0], $locales)) {
                        $locale = $segments[0];
                    }
                }
            }
        }

        if ($locale) {
            App::setLocale($locale);
            URL::defaults(['locale' => $locale]);
            session(['locale' => $locale]);
            // Save locale in a cookie for 1 year so it persists across sessions
            cookie()->queue(cookie('locale', $locale, 525600));
            $request->route()->forgetParameter('locale');
        }

        return $next($request);
    }
}
