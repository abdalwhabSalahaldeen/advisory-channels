@php
    $siteName = $site->tr('site_name');
    $pageTitle = trim($__env->yieldContent('title'));
    $pageTitle = $pageTitle ? "{$pageTitle} | {$siteName}" : $siteName;
    $metaDescription = trim($__env->yieldContent('description')) ?: $site->tr('meta_description');
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>document.documentElement.classList.add('js')</script>

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $alternates[$locale] ?? url()->current() }}">
@foreach ($alternates as $code => $url)
    <link rel="alternate" hreflang="{{ $code }}" href="{{ $url }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ $alternates[config('app.locale')] ?? url('/') }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_SA' : 'en_US' }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#050807">

<script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $site->tr('site_name', 'en'),
        'alternateName' => $site->tr('site_name', 'ar'),
        'url' => url('/'),
        'email' => $site->get('contact_email'),
        'foundingDate' => '2019',
        'areaServed' => 'SA',
        'sameAs' => array_values(array_filter([$site->get('linkedin_url'), $site->get('x_url')])),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@stack('head')

@fonts
@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
