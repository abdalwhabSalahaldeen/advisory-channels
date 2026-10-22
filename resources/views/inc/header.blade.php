@php
    $links = [
        ['route' => 'home', 'label' => __('header.home'), 'active' => 'home'],
        ['route' => 'about', 'label' => __('header.about'), 'active' => 'about'],
        ['route' => 'services.index', 'label' => __('header.services'), 'active' => 'services.*'],
        ['route' => 'saudi-gate', 'label' => __('header.saudi_gate'), 'active' => 'saudi-gate'],
        ['route' => 'careers.index', 'label' => __('header.careers'), 'active' => 'careers.*'],
        ['route' => 'contact', 'label' => __('header.contact'), 'active' => 'contact'],
    ];
@endphp

<header
    x-data="{ open: false, scrolled: false }"
    x-init="scrolled = window.scrollY > 24"
    @scroll.window="scrolled = window.scrollY > 24"
    @keydown.escape.window="open = false"
    class="fixed inset-x-0 top-0 z-50 transition-all duration-500"
    :class="scrolled || open ? 'bg-ink-950/85 backdrop-blur-xl shadow-lg shadow-black/10' : 'bg-transparent'"
>
    <div class="container-site flex h-20 items-center justify-between gap-6">
        <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ $site->tr('site_name') }}">
            <x-site.logo dark />
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ __('header.main') }}">
            @foreach ($links as $link)
                <a
                    href="{{ route($link['route']) }}"
                    @if (request()->routeIs($link['active'])) aria-current="page" @endif
                    @class([
                        'relative whitespace-nowrap rounded-full px-3 py-2 text-sm font-semibold transition hover:text-white xl:px-4',
                        'text-white after:absolute after:inset-x-3 after:-bottom-0.5 xl:after:inset-x-4 after:h-0.5 after:rounded-full after:bg-brand-400' => request()->routeIs($link['active']),
                        'text-white/70' => ! request()->routeIs($link['active']),
                    ])
                >{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            @if ($otherLocale)
                <a
                    href="{{ $alternates[$otherLocale] }}"
                    hreflang="{{ $otherLocale }}"
                    lang="{{ $otherLocale }}"
                    aria-label="{{ __('header.switch_language_label') }}"
                    class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-bold text-white/80 transition hover:bg-white/10 hover:text-white"
                >
                    <x-ui.icon name="globe" class="size-4" />
                    <span class="hidden xl:inline">{{ __('header.switch_language') }}</span>
                    <span class="xl:hidden">{{ $otherLocale === 'ar' ? 'ع' : 'EN' }}</span>
                </a>
            @endif

            <x-ui.button :href="route('request-service')" class="px-5! py-2.5! max-sm:hidden!">
                {{ __('header.request_service') }}
            </x-ui.button>

            <button
                type="button"
                class="inline-flex size-11 items-center justify-center rounded-full text-white hover:bg-white/10 lg:hidden"
                @click="open = ! open"
                :aria-expanded="open.toString()"
                aria-controls="mobile-menu"
            >
                <span class="sr-only" x-text="open ? @js(__('header.close')) : @js(__('header.menu'))">{{ __('header.menu') }}</span>
                <x-ui.icon name="menu" x-show="! open" />
                <x-ui.icon name="close" x-show="open" x-cloak />
            </button>
        </div>
    </div>

    <div
        id="mobile-menu"
        x-show="open"
        x-cloak
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="h-[calc(100dvh-5rem)] overflow-y-auto border-t border-white/10 lg:hidden"
    >
        <nav class="container-site flex flex-col gap-1 py-8" aria-label="{{ __('header.main') }}">
            @foreach ($links as $link)
                <a
                    href="{{ route($link['route']) }}"
                    @class([
                        'flex items-center justify-between border-b border-white/10 py-4 text-2xl font-bold',
                        'text-brand-400' => request()->routeIs($link['active']),
                        'text-white' => ! request()->routeIs($link['active']),
                    ])
                >
                    {{ $link['label'] }}
                    <x-ui.icon name="arrow" class="size-5 text-white/40 rtl:-scale-x-100" />
                </a>
            @endforeach

            <x-ui.button :href="route('request-service')" class="mt-8 w-full">
                {{ __('header.request_service') }}
            </x-ui.button>
        </nav>
    </div>
</header>
