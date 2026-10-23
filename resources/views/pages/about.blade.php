@extends('layouts.app')

@section('title', __('about.meta_title'))

@section('content')
    <x-ui.page-hero :image="asset(config('site.images.pages.about'))" :eyebrow="__('about.eyebrow')" :title="__('about.title')" :text="$site->tr('home_intro')" />

    {{-- Story --}}
    <section class="py-24 sm:py-32">
        <div class="container-site grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="eyebrow" data-reveal>{{ __('about.story_title') }}</p>
                <p class="mt-6 font-display text-8xl font-light tracking-tight text-brand-700/15 sm:text-9xl" data-reveal aria-hidden="true">2019</p>
            </div>
            <div class="space-y-6 text-xl leading-relaxed text-ink-700 lg:col-span-8">
                <p data-reveal>{{ $site->tr('about_story') }}</p>
                <p data-reveal>{{ $site->tr('about_team') }}</p>
            </div>
        </div>
    </section>

    {{-- Mission & vision --}}
    <section class="pb-24 sm:pb-32">
        <div class="container-site grid gap-5 lg:grid-cols-2">
            @foreach (['mission' => 'compass', 'vision' => 'star'] as $key => $icon)
                <div @class([
                    'relative overflow-hidden rounded-sm p-10 sm:p-14',
                    'bg-forest-950 text-white' => $loop->first,
                    'bg-forest-800 text-white' => $loop->last,
                ]) data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
                    <div class="pattern-star absolute inset-0 text-white/[0.03]" aria-hidden="true"></div>
                    <div class="relative">
                        <span class="inline-flex size-14 items-center justify-center rounded-sm bg-white/10 text-white ring-1 ring-white/20">
                            <x-ui.icon :name="$icon" class="size-7" />
                        </span>
                        <h2 class="mt-8 heading-display text-4xl">{{ __("about.{$key}") }}</h2>
                        <p class="mt-5 text-lg leading-relaxed text-white/80">{{ $site->tr($key) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Leadership (shown once profiles are added in the admin) --}}
    @if ($leaders->isNotEmpty())
        <section class="py-24 sm:py-32">
            <div class="container-site">
                <x-ui.section-heading :eyebrow="__('about.leadership_eyebrow')" :title="__('about.leadership_title')" />
                <div class="mt-16 grid gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($leaders as $leader)
                        <article class="group" x-data="{ open: false }" data-reveal data-reveal-delay="{{ ($loop->index % 4) * 0.08 }}">
                            <div class="relative aspect-[4/5] overflow-hidden bg-forest-900">
                                @if ($leader->photo)
                                    <img src="{{ Storage::disk('public')->url($leader->photo) }}" alt="{{ $leader->tr('name') }}" loading="lazy" class="size-full object-cover grayscale transition duration-[1.2s] ease-out-expo group-hover:scale-105 group-hover:grayscale-0">
                                @else
                                    <div class="pattern-star absolute inset-0 text-gold-300/10" aria-hidden="true"></div>
                                    <span class="absolute inset-0 flex items-center justify-center font-display text-7xl font-light text-gold-300/60" aria-hidden="true">{{ mb_substr($leader->tr('name'), 0, 1) }}</span>
                                @endif
                            </div>
                            <h3 class="heading-display mt-6 text-2xl">{{ $leader->tr('name') }}</h3>
                            <p class="mt-1 text-sm text-gold-600">{{ $leader->tr('position') }}</p>
                            @if ($bio = $leader->tr('bio'))
                                <p class="mt-4 leading-relaxed text-ink-700" :class="open ? '' : 'line-clamp-3'">{{ $bio }}</p>
                            @endif
                            @if ($leader->linkedin_url)
                                <a href="{{ $leader->linkedin_url }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 text-sm text-ink-700 transition hover:text-brand-700" aria-label="LinkedIn: {{ $leader->tr('name') }}">
                                    <x-ui.icon name="linkedin" class="size-4" /> LinkedIn
                                </a>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Values --}}
    <section class="bg-white py-24 sm:py-32">
        <div class="container-site">
            <x-ui.section-heading :title="__('about.values_title')" />
            <div class="mt-14 grid gap-px overflow-hidden rounded-sm border border-ink-900/5 bg-ink-900/5 sm:grid-cols-2 lg:grid-cols-5">
                @foreach (__('about.values') as $value)
                    <div class="bg-white p-8" data-reveal data-reveal-delay="{{ $loop->index * 0.06 }}">
                        <span class="font-latin text-sm font-medium text-brand-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-6 text-xl font-medium">{{ $value['title'] }}</h3>
                        <p class="mt-3 leading-relaxed text-ink-700">{{ $value['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- The palm --}}
    <section class="py-24 sm:py-32">
        <div class="container-site grid items-center gap-16 lg:grid-cols-2">
            <div class="bracket-frame relative mx-auto aspect-square w-full max-w-md overflow-hidden rounded-sm bg-forest-950" data-reveal>
                <div class="pattern-star absolute inset-0 text-white/[0.03]" aria-hidden="true"></div>
                <div class="absolute inset-0 m-auto size-72 rounded-full bg-brand-600/40 blur-[80px]" aria-hidden="true"></div>
                <svg viewBox="0 0 48 48" class="relative m-auto size-full p-20" aria-hidden="true">
                    <path d="M24 44c.6-7 .5-14 0-22" stroke="#6ee0a4" stroke-width="1.6" stroke-linecap="round" fill="none" />
                    <path d="M24 22c-3.5-4.6-8.8-6.2-14-4.8 4.2.4 8.4 2 11.2 5.2M24 22c3.5-4.6 8.8-6.2 14-4.8-4.2.4-8.4 2-11.2 5.2M24 22c-1.8-5.4-5.4-9-10.4-10.2 3.6 1.8 6.6 5 8 9M24 22c1.8-5.4 5.4-9 10.4-10.2-3.6 1.8-6.6 5-8 9M24 22c0-5.4 1.2-9.6 3.6-12.8-2.4 1.4-3.8 4.2-4.4 7.2" fill="none" stroke="#fff" stroke-width="1.4" stroke-linecap="round" />
                </svg>
            </div>
            <div>
                <x-ui.section-heading :title="__('about.palm_title')" :text="__('about.palm_text')" />
            </div>
        </div>
    </section>

    <x-ui.cta-band :title="__('index.cta_title')" :text="__('index.cta_text')">
        <x-ui.button :href="route('request-service')" variant="light">{{ __('header.request_service') }}</x-ui.button>
        <x-ui.button :href="route('services.index')" variant="outline-light">{{ __('global.actions.explore_services') }}</x-ui.button>
    </x-ui.cta-band>
@endsection
