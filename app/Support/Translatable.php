<?php

namespace App\Support;

class Translatable
{
    /**
     * Pick a translation out of a {"ar": "...", "en": "..."} value.
     *
     * Falls back to the app fallback locale, then to the first filled translation,
     * so a half-translated record never renders an empty heading.
     */
    public static function pick(mixed $value, ?string $locale = null): ?string
    {
        if (! is_array($value)) {
            return filled($value) ? (string) $value : null;
        }

        $locale ??= app()->getLocale();

        foreach ([$locale, config('app.fallback_locale')] as $candidate) {
            if (filled($value[$candidate] ?? null)) {
                return $value[$candidate];
            }
        }

        return collect($value)->first(fn ($translation) => filled($translation));
    }

    /**
     * Pick translations out of a list of {"ar": "...", "en": "..."} items.
     *
     * @return list<string>
     */
    public static function pickList(mixed $items, ?string $locale = null): array
    {
        return collect(is_array($items) ? $items : [])
            ->map(fn ($item) => static::pick($item, $locale))
            ->filter()
            ->values()
            ->all();
    }
}
