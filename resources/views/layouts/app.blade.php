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
        @include('inc.floating-contact')
        @include('inc.scripts')
    </body>
</html>
