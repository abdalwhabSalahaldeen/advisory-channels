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
     * The WhatsApp number in international form (9665XXXXXXXX), accepting local 05... input.
     */
    public function whatsappNumber(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->get('whatsapp'));

        return match (true) {
            $digits === '' => null,
            str_starts_with($digits, '00') => substr($digits, 2),
            str_starts_with($digits, '05') => '966'.substr($digits, 1),
            str_starts_with($digits, '5') && strlen($digits) === 9 => '966'.$digits,
            default => $digits,
        };
    }

    public function whatsappUrl(?string $text = null): ?string
    {
        $number = $this->whatsappNumber();

        return $number ? "https://wa.me/{$number}".($text ? '?text='.rawurlencode($text) : '') : null;
    }

    /**
     * The Saudi National Address as display lines in the current locale, or [] when not set.
     *
     * @return list<string>
     */
    public function nationalAddressLines(?string $locale = null): array
    {
        $address = (array) $this->get('national_address', []);
        $comma = ($locale ?? app()->getLocale()) === 'ar' ? '، ' : ', ';
        $part = fn (string $key) => is_array($address[$key] ?? null) ? Translatable::pick($address[$key], $locale) : ($address[$key] ?? null);

        $lines = [
            trim(implode(' ', array_filter([$part('building_number'), $part('street')]))),
            trim(implode($comma, array_filter([$part('district'), trim(implode(' ', array_filter([$part('city'), $part('postal_code')])))]))),
            $part('additional_number') ? __('contact.additional_number', ['number' => $part('additional_number')], $locale) : null,
            $part('short_address') ? __('contact.short_address', ['code' => $part('short_address')], $locale) : null,
        ];

        return array_values(array_filter($lines));
    }

    /**
     * A Google Maps embed for the "map location" setting (coordinates or a searchable address).
     */
    public function mapEmbedUrl(): ?string
    {
        $location = trim((string) $this->get('map_location'));

        return $location === '' ? null : 'https://maps.google.com/maps?q='.rawurlencode($location).'&z=16&output=embed&hl='.app()->getLocale();
    }

    /**
     * Where the address links to: the configured map link, or a Google Maps search for the map location.
     */
    public function mapLinkUrl(): ?string
    {
        $location = trim((string) $this->get('map_location'));

        return $this->get('map_url') ?: ($location === '' ? null : 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($location));
    }

    /**
     * Schema.org data for the firm as a local professional service (used by Google for the
     * knowledge panel and to match the site with the Google Business Profile).
     *
     * @return array<string, mixed>
     */
    public function organizationSchema(): array
    {
        $address = (array) $this->get('national_address', []);
        $street = trim(implode(' ', array_filter([$address['building_number'] ?? null, Translatable::pick($address['street'] ?? null, 'en')])));
        $location = (string) $this->get('map_location');

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => $this->tr('site_name', 'en'),
            'alternateName' => $this->tr('site_name', 'ar'),
            'url' => url('/'),
            'logo' => asset('images/brand/icon.svg'),
            'image' => asset(config('site.images.hero')),
            'email' => $this->get('contact_email'),
            'telephone' => $this->get('contact_phone'),
            'foundingDate' => '2019',
            'areaServed' => ['@type' => 'Country', 'name' => 'Saudi Arabia'],
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $street ?: null,
                'addressLocality' => Translatable::pick($address['city'] ?? null, 'en') ?: 'Riyadh',
                'postalCode' => $address['postal_code'] ?? null,
                'addressCountry' => 'SA',
            ]),
            'geo' => preg_match('/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/', $location, $coordinates)
                ? ['@type' => 'GeoCoordinates', 'latitude' => (float) $coordinates[1], 'longitude' => (float) $coordinates[2]]
                : null,
            'hasMap' => $this->mapLinkUrl(),
            'sameAs' => array_values(array_filter([$this->get('linkedin_url'), $this->get('x_url'), $this->get('google_business_url')])),
        ]);
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
