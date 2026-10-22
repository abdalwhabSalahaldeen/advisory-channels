@php
    $locale = app()->getLocale();
    $alternates = App\Support\Localization::alternateUrls();
    $otherLocale = collect($alternates)->keys()->first(fn ($code) => $code !== $locale);
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ App\Support\Localization::direction() }}" class="scroll-pt-24">
    <head>
        @include('inc.head')
    </head>
    <body class="min-h-dvh overflow-x-clip">
        <a href="#main" class="sr-only z-[100] rounded-full bg-brand-700 px-5 py-3 font-bold text-white focus:not-sr-only focus:fixed focus:start-4 focus:top-4">
            {{ __('global.skip_to_content') }}
        </a>

        @include('inc.header')

        <main id="main">
            @yield('content')
        </main>

        @include('inc.footer')

        {{-- Filled by resources/js/section-dots.js on wide screens. --}}
        <nav data-section-dots hidden class="fixed end-5 top-1/2 z-40 hidden -translate-y-1/2 flex-col items-center gap-1 rounded-full bg-ink-950/50 px-1 py-2 ring-1 ring-white/10 backdrop-blur-md lg:flex" aria-label="{{ __('global.sections_nav') }}"></nav>
        @include('inc.floating-contact')
        @include('inc.scripts')
    </body>
</html>
