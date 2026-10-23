<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Editable site-wide content (contact details, home/about copy, privacy policy...)
 * managed from the "Site settings" page in the admin panel.
 */
class SiteSettings
{
    private const CACHE_KEY = 'site-settings';

    /**
     * @var array<string, mixed>|null
     */
    private ?array $values = null;

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->values ??= Cache::rememberForever(
            self::CACHE_KEY,
            fn () => Setting::query()->pluck('value', 'key')->all(),
        );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->all(), $key, $default);
    }

    /**
     * Get a translatable setting in the given (or current) locale.
     */
    public function tr(string $key, ?string $locale = null): ?string
    {
        return Translatable::pick($this->get($key), $locale);
    }

    /**
     * The licences and accreditations marked as shown, with their name in the current locale.
     *
     * @return list<array{name: string, number: ?string, url: ?string, logo: ?string}>
     */
    public function credentials(): array
    {
        return collect($this->get('credentials', []))
            ->filter(fn (array $item) => ($item['visible'] ?? true) && Translatable::pick($item['name'] ?? null))
            ->map(fn (array $item) => [
                'name' => Translatable::pick($item['name']),
                'number' => $item['number'] ?? null,
                'url' => $item['url'] ?? null,
                'logo' => $item['logo'] ?? null,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function set(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);

        $this->values = null;
    }
}
