<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class Localization
{
    /**
     * The current page's URL in every supported locale, for hreflang tags and the language switcher.
     *
     * @return array<string, string>
     */
    public static function alternateUrls(): array
    {
        $route = Route::current();

        return collect(config('site.locales'))
            ->mapWithKeys(function (array $meta, string $locale) use ($route) {
                if (! $route?->getName() || ! in_array('locale', $route->parameterNames())) {
                    return [$locale => url($locale)];
                }

                return [$locale => route($route->getName(), [
                    ...$route->parameters(),
                    ...request()->query(),
                    'locale' => $locale,
                ])];
            })
            ->all();
    }

    public static function direction(?string $locale = null): string
    {
        return config('site.locales.'.($locale ?? app()->getLocale()).'.dir', 'ltr');
    }
}
