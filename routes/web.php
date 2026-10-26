<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use App\Http\Middleware\Localization;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\ArticleController;
$locales = ['az', 'en', 'ru', 'ka', 'fa', 'tr'];
$defaultLocale = 'az';

Route::get('/', function () use ($defaultLocale) {
    $locale = Cookie::get('locale') ?: session('locale') ?: $defaultLocale;
    $query = request()->getQueryString();
    $newUrl = '/' . $locale;
    if ($query) {
        $newUrl .= '?' . $query;
        }
    return redirect()->to($newUrl);
    });

Route::post('/get-token', [ApiController::class, 'getServiceToken'])->middleware(Localization::class);
Route::post('/callService/{module}/{service}', [ApiController::class, 'callApi'])->middleware([Localization::class, 'api.security']);

Route::group(['prefix' => '{locale}', 'middleware' => [Localization::class], 'where' => ['locale' => implode('|', $locales)]], function () {
    Route::get('/', [ArticleController::class, 'getCompanyLogosForIndex'])->name('home');
    Route::get('/registration', [ArticleController::class, 'getRegistrationArticles'])->name('registration');
    Route::get('/insurance-companies', [ArticleController::class, 'getContactArticles'])->name('insurance-companies');
    Route::get('/faq', [ArticleController::class, 'faq'])->name('faq');
    Route::get('/payment-status/{transId}', [ArticleController::class, 'status'])->name('payment-status');
    Route::get('/manual-guide', fn() => view('manual-guide'))->name('manual-guide');
    Route::get('/contact-us', fn() => view('contact-us'))->name('contact-us');
    Route::get('/about-us', fn() => view('about-us'))->name('about-us');
    //    Route::get('/tariffs', fn () => view('tariffs'))->name('tariffs');
    Route::get('/tariffs', fn() => abort(404))->name('tariffs');
});

// Global fallback for any unmatched route (both un-prefixed and deeply localized)
    Route::fallback(function () {
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Not Found.'], 404);
        }

    $locales = ['az', 'en', 'ru', 'ka', 'fa', 'tr'];
    $segment = request()->segment(1);

    // If the URL does not start with a valid language prefix, redirect to default (az)
    if (!in_array($segment, $locales)) {
        $path = request()->path();
        $query = request()->getQueryString();

        $locale = session('locale');

        if (!$locale) {
            $referer = request()->headers->get('referer');
            if ($referer) {
                $refererPath = parse_url($referer, PHP_URL_PATH);
                if ($refererPath) {
                    $segments = explode('/', ltrim($refererPath, '/'));
                    if (isset($segments[0]) && in_array($segments[0], $locales)) {
                        $locale = $segments[0];
                    }
                }
            }
        }

        if (!$locale) {
            $locale = 'az';
            }

        $newUrl = '/' . $locale . '/' . ($path === '/' ? '' : ltrim($path, '/'));
        if ($query) {
            $newUrl .= '?' . $query;
        }
        return redirect()->to($newUrl);
        }

    // Check if the current environment dictates a redirect instead of showing a 404
    $isTerminal = request()->query('source') === 'terminal';
    if (app()->environment('production') && !$isTerminal) {
        $locale = app()->getLocale() ?: 'az';
        return redirect()->to('/' . $locale);
    }

    // Otherwise, abort with a standard 404 page
    abort(404);
});
