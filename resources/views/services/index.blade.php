@extends('layouts.app')

@section('title', __('services.meta_title'))
@section('description', __('services.text'))

@section('content')
    <x-ui.page-hero :eyebrow="__('services.eyebrow')" :title="__('services.title')" :text="__('services.text')">
        {{-- Table of contents of the four channels --}}
        <nav class="mt-14 grid gap-px overflow-hidden border border-white/10 bg-white/10 sm:grid-cols-2 lg:grid-cols-4" aria-label="{{ __('services.channels') }}" data-reveal data-reveal-delay="0.35">
            @foreach ($services as $service)
                <a href="#{{ $service->slug }}" class="group flex items-center gap-4 bg-forest-950/80 p-5 transition hover:bg-forest-900">
                    <span class="font-latin text-xs text-gold-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="flex-1 text-sm text-white/80 transition group-hover:text-white">{{ $service->tr('title') }}</span>
                    <x-ui.icon name="arrow-down" class="size-4 text-white/30 transition group-hover:translate-y-0.5 group-hover:text-gold-300" />
                </a>
            @endforeach
        </nav>
    </x-ui.page-hero>

    {{-- Proof points --}}
    <section class="border-b border-ink-900/10 bg-white">
        <div class="container-site grid divide-y divide-ink-900/10 md:grid-cols-3 md:divide-x md:divide-y-0 rtl:md:divide-x-reverse">
            @foreach (__('services.proof') as $proof)
                <div class="flex items-start gap-5 py-10 md:px-10 md:first:ps-0 md:last:pe-0" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                    <span class="mt-2 h-10 w-px shrink-0 bg-gold-500" aria-hidden="true"></span>
                    <div>
                        <p class="heading-display text-3xl text-forest-900">{{ $proof['value'] }}</p>
                        <p class="mt-2 leading-relaxed text-ink-700">{{ $proof['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- The channels, one editorial chapter each, with a sticky index on wide screens --}}
    <section class="py-24 sm:py-32">
        <div class="container-site grid gap-16 lg:grid-cols-12">
            <aside class="hidden lg:col-span-3 lg:block">
                <nav class="sticky top-32" aria-label="{{ __('services.channels') }}" data-scrollspy>
                    <p class="eyebrow">{{ __('services.channels') }}</p>
                    <ol class="mt-8 space-y-1 border-s border-ink-900/10">
                        @foreach ($services as $service)
                            <li>
                                <a href="#{{ $service->slug }}" class="group relative -ms-px flex gap-4 border-s-2 border-transparent py-3 ps-5 text-ink-700 transition hover:text-ink-900 data-active:border-gold-500 data-active:text-forest-900">
                                    <span class="font-latin text-xs text-gold-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="leading-snug">{{ $service->tr('title') }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                    <x-ui.button :href="route('request-service')" variant="outline" class="mt-10 w-full">{{ __('header.request_service') }}</x-ui.button>
                </nav>
            </aside>

            <div class="lg:col-span-9">
                @foreach ($services as $service)
                    <article id="{{ $service->slug }}" @class(['scroll-mt-28', 'border-t border-ink-900/10 pt-24 mt-24' => ! $loop->first])>
                        {{-- Chapter heading --}}
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-10">
                            <span class="font-display text-6xl leading-none font-light text-gold-500/70 sm:text-8xl" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="sm:pt-2">
                                <p class="eyebrow" data-reveal>{{ __('services.channel') }} {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                <h2 class="heading-display mt-5 text-4xl leading-[1.15] sm:text-5xl" data-split>{{ $service->tr('title') }}</h2>
                                <p class="mt-6 max-w-2xl text-xl leading-[1.8] text-ink-700" data-reveal>{{ $service->tr('excerpt') }}</p>
                            </div>
                        </div>

                        {{-- Visual + narrative --}}
                        <div class="mt-14 grid gap-10 xl:grid-cols-5">
                            <div class="relative overflow-hidden bg-forest-950 xl:col-span-2" data-reveal-image>
                                @if ($service->image)
                                    <img src="{{ Storage::disk('public')->url($service->image) }}" alt="" loading="lazy" class="aspect-[16/6] size-full object-cover xl:aspect-auto xl:h-full">
                                @else
                                    {{-- Abstract panel until a photo is uploaded for the service. --}}
                                    <div class="relative flex aspect-[16/6] items-center justify-between p-8 xl:aspect-auto xl:h-full xl:min-h-80 xl:flex-col xl:items-start">
                                        <div class="pattern-star absolute inset-0 text-gold-300/10" aria-hidden="true"></div>
                                        <div class="absolute -end-20 -bottom-20 size-72 rounded-full bg-brand-600/30 blur-[80px]" aria-hidden="true"></div>
                                        <span class="relative inline-flex size-16 items-center justify-center rounded-full border border-gold-400/40 text-gold-300">
                                            <x-ui.icon :name="$service->icon ?? 'compass'" class="size-7" />
                                        </span>
                                        <span class="relative font-latin text-xs tracking-[0.3em] text-gold-300/70 uppercase rtl:tracking-normal">{{ __('services.channel') }} {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="xl:col-span-3">
                                @if ($intro = $service->intro())
                                    <p class="text-lg leading-[1.9] text-ink-700" data-reveal>{{ $intro }}</p>
                                @endif

                                @if ($highlights = $service->trList('highlights'))
                                    <p class="mt-10 text-sm font-medium text-ink-900" data-reveal>{{ __('services.highlights') }}</p>
                                    <ol class="mt-4 grid border-t border-ink-900/10 sm:grid-cols-2">
                                        @foreach ($highlights as $highlight)
                                            <li class="flex items-baseline gap-4 border-b border-ink-900/10 py-4 sm:odd:pe-6 sm:even:ps-6" data-reveal data-reveal-delay="{{ $loop->index * 0.05 }}">
                                                <span class="font-latin text-xs text-gold-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                <span class="leading-relaxed">{{ $highlight }}</span>
                                            </li>
                                        @endforeach
                                    </ol>
                                @endif

                                <div class="mt-10 flex flex-wrap items-center gap-3" data-reveal>
                                    <x-ui.button :href="route('services.show', $service)">{{ __('services.explore') }}</x-ui.button>
                                    <x-ui.button :href="route('request-service', ['service' => $service->slug])" variant="outline" icon="">{{ __('global.actions.request_this_service') }}</x-ui.button>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How we work --}}
    <section class="relative overflow-hidden bg-forest-900 py-24 text-white sm:py-32">
        <div class="pattern-star absolute inset-0 text-white/[0.03]" aria-hidden="true"></div>
        <div class="container-site relative grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-ui.section-heading dark :eyebrow="__('services.approach_eyebrow')" :title="__('index.method_title')" :text="__('index.method_text')" />
            </div>
            <ol class="grid gap-px bg-white/10 sm:grid-cols-2 lg:col-span-8">
                @foreach (__('index.method') as $step)
                    <li class="bg-forest-900 p-8" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                        <span class="font-display text-4xl font-light text-gold-400">{{ $loop->iteration }}</span>
                        <h3 class="mt-6 text-xl">{{ $step['title'] }}</h3>
                        <p class="mt-3 leading-relaxed text-white/60">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Cross-link to Saudi Gate --}}
    <section class="relative overflow-hidden bg-sand-100 py-24 sm:py-32">
        <div class="pattern-star absolute inset-0 text-gold-500/10" aria-hidden="true"></div>
        <div class="container-site relative grid items-center gap-14 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <x-ui.section-heading :eyebrow="__('services.gate_eyebrow')" :title="__('services.gate_title')" :text="__('services.gate_text')" />
                <x-ui.button :href="route('saudi-gate')" class="mt-10" data-reveal>{{ __('global.actions.explore_gate') }}</x-ui.button>
            </div>
            <ol class="grid grid-cols-2 gap-px bg-gold-500/25 sm:grid-cols-4 lg:col-span-7">
                @foreach ($packages as $package)
                    <li class="bg-sand-50" data-reveal data-reveal-delay="{{ $loop->index * 0.05 }}">
                        <a href="{{ route('saudi-gate') }}#{{ $package->slug }}" class="group flex h-full flex-col p-5 transition hover:bg-white">
                            <span class="font-latin text-xs text-gold-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="heading-display mt-8 text-xl transition group-hover:text-brand-700">{{ $package->tr('title') }}</span>
                        </a>
                    </li>
                @endforeach
                <li class="flex items-center justify-center bg-forest-900 p-5">
                    <a href="{{ route('saudi-gate') }}#gate-form" class="text-center text-sm text-gold-300 underline-offset-4 hover:underline">{{ __('gate.form_eyebrow') }}</a>
                </li>
            </ol>
        </div>
    </section>

    <x-ui.cta-band :title="__('index.cta_title')" :text="__('index.cta_text')">
        <x-ui.button :href="route('request-service')" variant="gold">{{ __('header.request_service') }}</x-ui.button>
        <x-ui.button :href="route('contact')" variant="outline-light">{{ __('global.actions.lets_talk') }}</x-ui.button>
    </x-ui.cta-band>
@endsection
