<?php

use App\Http\Controllers\CareerController;
use App\Http\Controllers\CaseStudyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\SetLocale;
use App\Support\Localization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/'.Localization::defaultLocale());

Route::get('sitemap.xml', SitemapController::class)->name('sitemap');

Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('site.locales')))
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('about', [HomeController::class, 'about'])->name('about');
        Route::get('saudi-gate', [HomeController::class, 'saudiGate'])->name('saudi-gate');
        Route::get('privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
        Route::get('terms-and-conditions', [HomeController::class, 'termsAndConditions'])->name('terms-and-conditions');

        Route::get('faqs', [FaqController::class, 'index'])->name('faqs');
        Route::get('contact', [ContactController::class, 'index'])->name('contact');
        Route::get('request-service', [ContactController::class, 'requestService'])->name('request-service');

        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('services/{service}', [ServiceController::class, 'show'])->name('services.show');

        Route::get('insights', [InsightController::class, 'index'])->name('insights.index');
        Route::get('insights/{insight}', [InsightController::class, 'show'])->name('insights.show');

        Route::get('impact', [CaseStudyController::class, 'index'])->name('impact.index');
        Route::get('impact/{caseStudy}', [CaseStudyController::class, 'show'])->name('impact.show');

        Route::get('careers', [CareerController::class, 'index'])->name('careers.index');
        Route::get('careers/apply', [CareerController::class, 'apply'])->name('careers.apply');
        Route::get('careers/{jobOpening}', [CareerController::class, 'show'])->name('careers.show');
    });

/*
|--------------------------------------------------------------------------
| Legacy Wix URLs
|--------------------------------------------------------------------------
|
| Keep the search rankings and shared links of the old site. Wix served every
| page in English with an optional ?lang= query string.
|
*/

foreach ([
    'about-3' => 'about',
    'services-6' => 'services',
    'saudi-gate' => 'saudi-gate',
    'general-5' => 'careers',
    'general-8' => 'privacy-policy',
] as $legacyPath => $path) {
    Route::get($legacyPath, function (Request $request) use ($path) {
        $locale = $request->query('lang') === 'ar' ? 'ar' : 'en';

        return redirect("/{$locale}/{$path}", 301);
    });
}
