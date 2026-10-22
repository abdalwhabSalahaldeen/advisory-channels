<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Locked;

/**
 * Livewire update requests hit /livewire/update, which has no {locale} prefix,
 * so the component carries the page locale across requests itself.
 */
trait RemembersLocale
{
    #[Locked]
    public string $locale = '';

    public function mountRemembersLocale(): void
    {
        $this->locale = app()->getLocale();

        $this->applyLocale();
    }

    public function hydrateRemembersLocale(): void
    {
        $this->applyLocale();
    }

    protected function applyLocale(): void
    {
        if (array_key_exists($this->locale, config('site.locales'))) {
            app()->setLocale($this->locale);
            URL::defaults(['locale' => $this->locale]);
        }
    }
}
