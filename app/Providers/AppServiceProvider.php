<?php

namespace App\Providers;

use App\Models\CaseStudy;
use App\Models\GatePackage;
use App\Models\Service;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(SiteSettings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', fn ($view) => $view->with('site', $this->app->make(SiteSettings::class)));

        // The header's mega menus list the service channels and Saudi Gate stages on every page.
        View::composer('inc.header', fn ($view) => $view->with([
            'navServices' => Service::query()->published()->get(),
            'navPackages' => GatePackage::query()->published()->get(),
            'navHasImpact' => CaseStudy::query()->where('is_active', true)->exists(),
        ]));
    }
}
