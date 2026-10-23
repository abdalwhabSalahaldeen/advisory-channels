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
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
<link rel="icon" href="{{ asset('images/brand/icon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $alternates[$locale] ?? url()->current() }}">
@foreach ($alternates as $code => $url)
    <link rel="alternate" hreflang="{{ $code }}" href="{{ $url }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ $alternates[App\Support\Localization::defaultLocale()] ?? url('/') }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset(config('site.images.hero')) }}">
<meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_SA' : 'en_US' }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#050807">

@if ($verification = $site->get('google_site_verification'))
    <meta name="google-site-verification" content="{{ $verification }}">
@endif

<script type="application/ld+json">
    {!! json_encode($site->organizationSchema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@stack('head')

@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
@include('inc.analytics')
