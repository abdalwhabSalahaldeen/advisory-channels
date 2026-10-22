<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Basic spam protection for public forms: a honeypot field and a per-IP rate limit.
 */
trait GuardsSubmissions
{
    /**
     * Honeypot: hidden from people, so only bots fill it in.
     */
    public string $website = '';

    /**
     * Returns false when the submission should be silently dropped.
     *
     * @throws ValidationException when the visitor submits too often
     */
    protected function passesGuards(): bool
    {
        if ($this->website !== '') {
            return false;
        }

        $key = static::class.':'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, maxAttempts: 5)) {
            throw ValidationException::withMessages([
                'form' => __('site.forms.too_many_attempts', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        RateLimiter::hit($key, decaySeconds: 600);

        return true;
    }
}
