<?php

namespace App\Filament\Support;

/**
 * Select options for icons drawn by the site's <x-ui.icon> component.
 */
class Icons
{
    /**
     * @param  list<string>  $names
     * @return array<string, string>
     */
    public static function options(array $names): array
    {
        return collect($names)
            ->mapWithKeys(fn (string $icon): array => [$icon => __("admin.icons.{$icon}")])
            ->all();
    }
}
