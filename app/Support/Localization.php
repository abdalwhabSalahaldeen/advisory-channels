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

    /**
     * The site's default locale. Unlike config('app.locale'), this does not
     * change when a request switches the app to another language.
     */
    public static function defaultLocale(): string
    {
        return array_key_first(config('site.locales'));
    }

    public static function direction(?string $locale = null): string
    {
        return config('site.locales.'.($locale ?? app()->getLocale()).'.dir', 'ltr');
    }
}
