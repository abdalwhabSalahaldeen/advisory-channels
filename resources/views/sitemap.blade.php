{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($entries as $entry)
@foreach ($locales as $locale)
    <url>
        <loc>{{ route($entry['route'], [...$entry['parameters'], 'locale' => $locale]) }}</loc>
@foreach ($locales as $alternate)
        <xhtml:link rel="alternate" hreflang="{{ $alternate }}" href="{{ route($entry['route'], [...$entry['parameters'], 'locale' => $alternate]) }}" />
@endforeach
@isset($entry['updated'])
        <lastmod>{{ $entry['updated']->toAtomString() }}</lastmod>
@endisset
    </url>
@endforeach
@endforeach
</urlset>
