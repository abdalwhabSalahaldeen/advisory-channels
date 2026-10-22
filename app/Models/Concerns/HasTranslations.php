<?php

namespace App\Models\Concerns;

use App\Support\Translatable;

trait HasTranslations
{
    /**
     * Get a translatable attribute in the given (or current) locale.
     */
    public function tr(string $attribute, ?string $locale = null): ?string
    {
        return Translatable::pick($this->getAttribute($attribute), $locale);
    }

    /**
     * Get a translatable list attribute (repeater items) in the given (or current) locale.
     *
     * @return list<string>
     */
    public function trList(string $attribute, ?string $locale = null): array
    {
        return Translatable::pickList($this->getAttribute($attribute), $locale);
    }
}
