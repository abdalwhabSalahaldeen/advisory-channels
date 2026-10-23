@php
    $links = [
        ['route' => 'about', 'label' => __('header.about'), 'active' => 'about'],
        ['route' => 'services.index', 'label' => __('header.services'), 'active' => 'services.*', 'menu' => 'services'],
        ['route' => 'saudi-gate', 'label' => __('header.saudi_gate'), 'active' => 'saudi-gate', 'menu' => 'gate'],
        ['route' => 'insights.index', 'label' => __('header.insights'), 'active' => 'insights.*'],
        ...($navHasImpact ? [['route' => 'impact.index', 'label' => __('header.impact'), 'active' => 'impact.*']] : []),
        ['route' => 'careers.index', 'label' => __('header.careers'), 'active' => 'careers.*'],
        ['route' => 'contact', 'label' => __('header.contact'), 'active' => 'contact'],
    ];
@endphp

<header
    x-data="{ open: false, menu: null, scrolled: false, timer: null }"
    x-init="scrolled = window.scrollY > 24"
    @scroll.window="scrolled = window.scrollY > 24"
    @keydown.escape.window="open = false; menu = null"
    class="fixed inset-x-0 top-0 z-50 transition-transform duration-700 ease-out-expo"
    :class="scrolled && ! open ? 'lg:-translate-y-10' : ''"
    @mouseleave="clearTimeout(timer); timer = setTimeout(() => menu = null, 200)"
    @click.outside="menu = null"
>
    {{-- Utility bar --}}
    <div class="hidden h-10 border-b border-white/10 bg-forest-950 text-xs text-white/60 lg:block">
        <div class="container-site flex h-full items-center justify-between">
            <p class="tracking-wide">{{ __('header.based_in') }}</p>
            <div class="flex items-center gap-6">
                @if ($email = $site->get('contact_email'))
                    <a href="mailto:{{ $email }}" class="inline-flex items-center gap-2 transition hover:text-gold-300" dir="ltr">
                        <x-ui.icon name="mail" class="size-3.5" /> {{ $email }}
                    </a>
                @endif
                @if ($phone = $site->get('contact_phone'))
                    <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="inline-flex items-center gap-2 transition hover:text-gold-300" dir="ltr">
                        <x-ui.icon name="phone" class="size-3.5" /> {{ $phone }}
                    </a>
                @endif
                @if ($linkedin = $site->get('linkedin_url'))
                    <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="transition hover:text-gold-300" aria-label="LinkedIn"><x-ui.icon name="linkedin" class="size-3.5" /></a>
                @endif
                @if ($x = $site->get('x_url'))
                    <a href="{{ $x }}" target="_blank" rel="noopener" class="transition hover:text-gold-300" aria-label="X"><x-ui.icon name="x" class="size-3.5" /></a>
                @endif
                @if ($otherLocale)
                    <a href="{{ $alternates[$otherLocale] }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}" class="inline-flex items-center gap-2 border-s border-white/15 ps-6 font-medium text-white/80 transition hover:text-gold-300" aria-label="{{ __('header.switch_language_label') }}">
                        <x-ui.icon name="globe" class="size-3.5" /> {{ __('header.switch_language') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Main bar --}}
    <div class="transition-colors duration-500" :class="scrolled || open || menu ? 'bg-forest-950/95 backdrop-blur-xl shadow-[0_10px_40px_-20px_rgb(0_0_0/0.5)]' : 'bg-transparent'">
        <div class="container-site flex h-20 items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ $site->tr('site_name') }}">
                <x-site.logo dark />
            </a>

            <nav class="hidden items-center lg:flex" aria-label="{{ __('header.main') }}">
                @foreach ($links as $link)
                    @if ($link['menu'] ?? null)
                        <button
                            type="button"
                            @mouseenter="clearTimeout(timer); menu = @js($link['menu'])"
                            @click="menu = @js($link['menu'])"
                            :aria-expanded="(menu === @js($link['menu'])).toString()"
                            aria-controls="mega-{{ $link['menu'] }}"
                            @class([
                                'relative inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-2 text-sm transition hover:text-white xl:px-4',
                                'text-white' => request()->routeIs($link['active']),
                                'text-white/70' => ! request()->routeIs($link['active']),
                            ])
                        >
                            {{ $link['label'] }}
                            <svg viewBox="0 0 24 24" class="size-3.5 transition-transform duration-300" :class="menu === @js($link['menu']) && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
                        </button>
                    @else
                        <a
                            href="{{ route($link['route']) }}"
                            @mouseenter="clearTimeout(timer); menu = null"
                            @if (request()->routeIs($link['active'])) aria-current="page" @endif
                            @class([
                                'relative whitespace-nowrap px-3 py-2 text-sm transition hover:text-white xl:px-4',
                                'text-white after:absolute after:inset-x-3 after:-bottom-1 after:h-px after:bg-gold-400 xl:after:inset-x-4' => request()->routeIs($link['active']),
                                'text-white/70' => ! request()->routeIs($link['active']),
                            ])
                        >{{ $link['label'] }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                @if ($otherLocale)
                    <a href="{{ $alternates[$otherLocale] }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}" aria-label="{{ __('header.switch_language_label') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm text-white/80 transition hover:text-white lg:hidden">
                        <x-ui.icon name="globe" class="size-4" />
                        <span>{{ $otherLocale === 'ar' ? 'ع' : 'EN' }}</span>
                    </a>
                @endif

                <x-ui.button :href="route('request-service')" variant="gold" class="px-5! py-3! max-sm:hidden!">
                    {{ __('header.request_service') }}
                </x-ui.button>

                <button
                    type="button"
                    class="inline-flex size-11 items-center justify-center text-white lg:hidden"
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

        {{-- Mega menus (desktop) --}}
        <div
            id="mega-services"
            x-show="menu === 'services'"
            x-cloak
            x-transition:enter="transition duration-500 ease-out-expo"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:leave="transition duration-200"
            x-transition:leave-end="opacity-0"
            @mouseenter="clearTimeout(timer)"
            class="hidden border-t border-white/10 lg:block"
        >
            <div class="container-site grid grid-cols-12 gap-10 py-12">
                <div class="col-span-4">
                    <p class="heading-display text-3xl text-white">{{ __('header.mega_services_title') }}</p>
                    <p class="mt-4 leading-relaxed text-white/60">{{ __('header.mega_services_text') }}</p>
                    <x-ui.button :href="route('services.index')" variant="outline-light" class="mt-8">{{ __('header.all_services') }}</x-ui.button>
                </div>
                <ul class="col-span-8 grid grid-cols-2 gap-px bg-white/10">
                    @foreach ($navServices as $navService)
                        <li class="bg-forest-950">
                            <a href="{{ route('services.show', $navService) }}" class="group flex h-full gap-5 p-6 transition hover:bg-white/[0.04]">
                                <span class="font-latin text-sm text-gold-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span>
                                    <span class="block text-lg text-white transition group-hover:text-gold-300">{{ $navService->tr('title') }}</span>
                                    <span class="mt-2 block text-sm leading-relaxed text-white/50">{{ $navService->tr('excerpt') }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div
            id="mega-gate"
            x-show="menu === 'gate'"
            x-cloak
            x-transition:enter="transition duration-500 ease-out-expo"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:leave="transition duration-200"
            x-transition:leave-end="opacity-0"
            @mouseenter="clearTimeout(timer)"
            class="hidden border-t border-white/10 lg:block"
        >
            <div class="container-site grid grid-cols-12 gap-10 py-12">
                <div class="col-span-4">
                    <p class="heading-display text-3xl text-white">{{ __('header.mega_gate_title') }}</p>
                    <p class="mt-4 leading-relaxed text-white/60">{{ __('header.mega_gate_text') }}</p>
                    <x-ui.button :href="route('saudi-gate')" variant="outline-light" class="mt-8">{{ __('header.explore_journey') }}</x-ui.button>
                </div>
                <ol class="col-span-8 grid grid-cols-4 gap-px bg-white/10">
                    @foreach ($navPackages as $navPackage)
                        <li class="bg-forest-950">
                            <a href="{{ route('saudi-gate') }}#{{ $navPackage->slug }}" class="group flex h-full flex-col p-5 transition hover:bg-white/[0.04]">
                                <span class="font-latin text-xs text-gold-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="mt-6 text-lg text-white transition group-hover:text-gold-300">{{ $navPackage->tr('title') }}</span>
                                <span class="mt-1 text-xs leading-relaxed text-white/45">{{ $navPackage->tr('tagline') }}</span>
                            </a>
                        </li>
                    @endforeach
                    <li class="flex items-center justify-center bg-forest-900">
                        <a href="{{ route('saudi-gate') }}#gate-form" class="text-sm text-gold-300 underline-offset-4 hover:underline">{{ __('gate.form_eyebrow') }}</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div
        id="mobile-menu"
        x-show="open"
        x-cloak
        x-transition:enter="transition duration-300 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        data-lenis-prevent
        class="h-[calc(100dvh-5rem)] overflow-y-auto bg-forest-950 lg:hidden"
    >
        <nav class="container-site flex flex-col py-8" aria-label="{{ __('header.main') }}">
            @foreach ([['route' => 'home', 'label' => __('header.home'), 'active' => 'home'], ...$links] as $link)
                <a
                    href="{{ route($link['route']) }}"
                    @class([
                        'flex items-center justify-between border-b border-white/10 py-5 text-2xl font-light',
                        'text-gold-300' => request()->routeIs($link['active']),
                        'text-white' => ! request()->routeIs($link['active']),
                    ])
                >
                    {{ $link['label'] }}
                    <x-ui.icon name="arrow" class="size-5 text-white/30 rtl:-scale-x-100" />
                </a>
            @endforeach

            <x-ui.button :href="route('request-service')" variant="gold" class="mt-10 w-full">
                {{ __('header.request_service') }}
            </x-ui.button>
        </nav>
    </div>
</header>
