@extends('layouts.app')

@section('title', __('index.meta_title'))

@section('content')
    {{-- Hero --}}
    <section id="home" class="relative isolate flex min-h-dvh items-center overflow-hidden bg-ink-950 pt-28 pb-20 text-white">
        <canvas data-network class="absolute inset-0 -z-10 size-full" aria-hidden="true"></canvas>
        <div class="grid-lines absolute inset-0 -z-10" aria-hidden="true"></div>
        <div class="absolute -top-48 start-1/3 -z-10 size-[48rem] rounded-full bg-brand-700/35 blur-[140px]" aria-hidden="true"></div>
        <div class="absolute inset-x-0 bottom-0 -z-10 h-40 bg-gradient-to-t from-ink-950 to-transparent" aria-hidden="true"></div>

        <div class="container-site grid items-center gap-16 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="eyebrow text-brand-400!" data-reveal>{{ __('global.brand_tagline') }}</p>
                <h1 class="bracket-frame mt-8 text-5xl font-extrabold leading-[1.1] tracking-tight sm:text-6xl lg:text-7xl" data-reveal data-reveal-delay="0.1">
                    @foreach (preg_split('/[،,]\s*/u', $site->tr('hero_title')) as $line)
                        <span @class(['block', 'text-brand-400' => $loop->iteration === 2])>{{ $line }}</span>
                    @endforeach
                </h1>
                <p class="mt-8 max-w-2xl text-lg leading-relaxed text-white/70 text-pretty" data-reveal data-reveal-delay="0.2">
                    {{ $site->tr('hero_subtitle') }}
                </p>
                <div class="mt-10 flex flex-wrap gap-3" data-reveal data-reveal-delay="0.3">
                    <x-ui.button :href="route('request-service')">{{ __('header.request_service') }}</x-ui.button>
                    <x-ui.button :href="route('saudi-gate')" variant="outline-light">{{ __('global.actions.explore_gate') }}</x-ui.button>
                </div>
            </div>

            {{-- Service channels as a compact "control panel" next to the headline. --}}
            <div class="hidden lg:col-span-5 lg:block" data-reveal data-reveal-delay="0.25">
                <div class="relative rounded-3xl border border-white/10 bg-white/[0.03] p-3 backdrop-blur-sm">
                    @foreach ($services as $service)
                        <a href="{{ route('services.show', $service) }}" class="group flex items-center gap-5 rounded-2xl p-5 transition hover:bg-white/[0.06]">
                            <span class="font-latin text-sm font-bold text-brand-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="inline-flex size-12 items-center justify-center rounded-xl bg-brand-700/30 text-brand-300 ring-1 ring-brand-400/20">
                                <x-ui.icon :name="$service->icon ?? 'compass'" />
                            </span>
                            <span class="flex-1 font-bold">{{ $service->tr('title') }}</span>
                            <x-ui.icon name="arrow" class="size-4 text-white/30 transition group-hover:text-brand-400 rtl:-scale-x-100" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <a href="#intro" class="absolute bottom-8 start-1/2 hidden -translate-x-1/2 flex-col items-center gap-2 text-xs font-semibold text-white/50 transition hover:text-white sm:flex rtl:translate-x-1/2">
            {{ __('index.scroll') }}
            <x-ui.icon name="arrow-down" class="size-4 animate-bounce motion-reduce:animate-none" />
        </a>
    </section>

    {{-- Partners --}}
    @if ($partners->isNotEmpty())
        <section class="border-b border-ink-900/5 bg-white py-12" aria-label="{{ __('index.partners_title') }}">
            <div class="container-site">
                <p class="text-center text-sm font-bold text-ink-700/60">{{ __('index.partners_title') }}</p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-14 gap-y-8">
                    @foreach ($partners as $partner)
                        @php($logo = '<img src="'.e(Storage::disk('public')->url($partner->logo)).'" alt="'.e($partner->name).'" class="h-10 w-auto opacity-60 grayscale transition hover:opacity-100 hover:grayscale-0" loading="lazy">')
                        @if ($partner->url)
                            <a href="{{ $partner->url }}" target="_blank" rel="noopener">{!! $logo !!}</a>
                        @else
                            {!! $logo !!}
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Intro --}}
    <section id="intro" class="relative py-24 sm:py-32">
        <div class="container-site grid gap-16 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-7">
                <p class="eyebrow" data-reveal>{{ __('index.intro_eyebrow') }}</p>
                <h2 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight text-balance sm:text-4xl lg:text-5xl" data-reveal>
                    {{ __('index.intro_title') }}
                </h2>
                <p class="mt-8 text-lg leading-relaxed text-ink-700" data-reveal>{{ $site->tr('home_intro') }}</p>
                <p class="mt-5 text-lg leading-relaxed text-ink-700" data-reveal>{{ $site->tr('about_team') }}</p>
                <div class="mt-10" data-reveal>
                    <x-ui.button :href="route('about')" variant="outline">{{ __('global.actions.learn_more') }}</x-ui.button>
                </div>
            </div>

            <div class="lg:col-span-5" data-reveal data-reveal-delay="0.15">
                <div class="bracket-frame relative min-h-96 overflow-hidden rounded-3xl bg-ink-950 p-8 text-white sm:p-10">
                    <div class="grid-lines absolute inset-0" aria-hidden="true"></div>
                    <div class="absolute -bottom-24 -end-24 size-80 rounded-full bg-brand-600/40 blur-[90px]" aria-hidden="true"></div>
                    <div class="relative flex min-h-80 flex-col justify-between gap-10">
                        <span class="inline-flex w-fit items-center gap-2 rounded-full border border-brand-400/30 bg-brand-400/10 px-4 py-2 text-sm font-bold text-brand-300">
                            <x-ui.icon name="star" class="size-4" />
                            {{ __('index.customize_to_fit') }}
                        </span>
                        <p class="text-xl leading-relaxed font-bold text-balance sm:text-2xl">{{ $site->tr('vision') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Counters --}}
    @if ($stats = $site->get('home_stats'))
        <section id="counters" class="border-y border-ink-900/5 bg-white">
            <div class="container-site grid grid-cols-2 lg:grid-cols-4">
                @foreach ($stats as $stat)
                    @php(preg_match('/^(\D*)(\d+)(\D*)$/u', (string) $stat['value'], $parts))
                    <div @class(['px-4 py-12 sm:px-8', 'border-s border-ink-900/5' => ! $loop->first]) data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                        <p class="font-latin text-4xl font-extrabold tracking-tight text-brand-700 sm:text-5xl" dir="ltr">
                            {{-- Animate counts, but not years like "2019". --}}
                            @if ($parts && (int) $parts[2] < 1000)
                                {{ $parts[1] }}<span data-counter="{{ $parts[2] }}">{{ $parts[2] }}</span>{{ $parts[3] }}
                            @else
                                {{ $stat['value'] }}
                            @endif
                        </p>
                        <p class="mt-3 text-sm font-semibold text-ink-700">{{ App\Support\Translatable::pick($stat['label'] ?? null) }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Services --}}
    <section id="services" class="py-24 sm:py-32">
        <div class="container-site">
            <div class="flex flex-col justify-between gap-8 lg:flex-row lg:items-end">
                <x-ui.section-heading :eyebrow="__('index.services_eyebrow')" :title="__('index.services_title')" :text="__('index.services_text')" />
                <x-ui.button :href="route('services.index')" variant="outline" class="shrink-0" data-reveal>{{ __('global.actions.all_services') }}</x-ui.button>
            </div>

            <div class="mt-16 grid gap-5 md:grid-cols-2">
                @foreach ($services as $service)
                    <a
                        href="{{ route('services.show', $service) }}"
                        class="card-lift group relative flex flex-col overflow-hidden rounded-3xl border border-ink-900/5 bg-white p-8 sm:p-10"
                        data-reveal
                        data-reveal-delay="{{ ($loop->index % 2) * 0.1 }}"
                    >
                        <div class="flex items-start justify-between">
                            <span class="inline-flex size-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-700 transition duration-500 group-hover:bg-brand-700 group-hover:text-white">
                                <x-ui.icon :name="$service->icon ?? 'compass'" class="size-7" />
                            </span>
                            <span class="font-latin text-5xl font-extrabold text-ink-900/5 transition group-hover:text-brand-700/10">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="mt-8 text-2xl font-extrabold">{{ $service->tr('title') }}</h3>
                        <p class="mt-3 leading-relaxed text-ink-700">{{ $service->tr('excerpt') }}</p>
                        <ul class="mt-6 flex flex-wrap gap-2">
                            @foreach (array_slice($service->trList('highlights'), 0, 3) as $highlight)
                                <li class="rounded-full bg-paper px-3 py-1.5 text-xs font-semibold text-ink-700">{{ $highlight }}</li>
                            @endforeach
                        </ul>
                        <span class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-brand-700">
                            {{ __('global.actions.learn_more') }}
                            <x-ui.icon name="arrow" class="size-4 transition group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Methodology (the "steps" section of the Drovox home page) --}}
    <section id="steps" class="relative overflow-hidden bg-ink-950 py-24 text-white sm:py-32">
        <div class="grid-lines absolute inset-0" aria-hidden="true"></div>
        <div class="container-site relative">
            <x-ui.section-heading dark align="center" :eyebrow="__('index.method_eyebrow')" :title="__('index.method_title')" :text="__('index.method_text')" />

            <div class="relative mt-20">
                <div class="absolute inset-x-0 top-7 hidden h-px bg-gradient-to-r from-transparent via-brand-500/50 to-transparent lg:block" aria-hidden="true"></div>
                <ol class="relative grid gap-10 md:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                    @foreach (__('index.method') as $step)
                        <li class="relative" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
                            <span class="relative inline-flex size-14 items-center justify-center rounded-full border border-brand-500/40 bg-ink-900 font-latin text-lg font-extrabold text-brand-400">
                                {{ $loop->iteration }}
                            </span>
                            <h3 class="mt-6 text-xl font-extrabold">{{ $step['title'] }}</h3>
                            <p class="mt-3 leading-relaxed text-white/60">{{ $step['text'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- Saudi Gate teaser --}}
    <section id="saudi-gate" class="overflow-hidden py-24 sm:py-32">
        <div class="container-site">
            <div class="flex flex-col justify-between gap-8 lg:flex-row lg:items-end">
                <x-ui.section-heading :eyebrow="__('index.gate_eyebrow')" :title="__('index.gate_title')" :text="__('index.gate_text')" />
                <x-ui.button :href="route('saudi-gate')" class="shrink-0" data-reveal>{{ __('global.actions.explore_gate') }}</x-ui.button>
            </div>
        </div>

        <div class="container-site mt-14" data-reveal>
            <div class="swiper overflow-visible!" data-swiper>
                <div class="swiper-wrapper">
                    @foreach ($packages as $package)
                        <div class="swiper-slide h-auto!">
                            <a href="{{ route('saudi-gate') }}#{{ $package->slug }}" @class([
                                'group flex h-full flex-col rounded-3xl border p-7 transition duration-500 hover:-translate-y-1',
                                'border-sand-500/40 bg-gradient-to-b from-sand-100 to-white' => $package->is_premium,
                                'border-ink-900/5 bg-white hover:border-brand-500/40' => ! $package->is_premium,
                            ])>
                                <div class="flex items-center justify-between">
                                    <span class="font-latin text-sm font-bold text-brand-700">{{ __('gate.stage') }} {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    @if ($package->is_premium)
                                        <span class="rounded-full bg-sand-500 px-3 py-1 text-xs font-bold text-white">{{ __('gate.premium') }}</span>
                                    @endif
                                </div>
                                <h3 class="mt-10 text-3xl font-extrabold">{{ $package->tr('title') }}</h3>
                                <p class="mt-3 leading-relaxed text-ink-700">{{ $package->tr('tagline') }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Who we serve --}}
    <section id="overview" class="bg-white py-24 sm:py-32">
        <div class="container-site">
            <x-ui.section-heading align="center" :eyebrow="__('index.audience_eyebrow')" :title="__('index.audience_title')" />

            <div class="mt-16 grid gap-5 lg:grid-cols-3">
                @foreach (__('index.audiences') as $audience)
                    <div class="rounded-3xl bg-paper p-8 sm:p-10" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
                        <span class="inline-flex size-12 items-center justify-center rounded-xl bg-brand-700 text-white">
                            <x-ui.icon :name="['building', 'briefcase', 'rocket'][$loop->index] ?? 'star'" />
                        </span>
                        <h3 class="mt-6 text-xl font-extrabold">{{ $audience['title'] }}</h3>
                        <p class="mt-3 leading-relaxed text-ink-700">{{ $audience['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section id="testimonials" class="overflow-hidden py-24 sm:py-32">
            <div class="container-site">
                <x-ui.section-heading :eyebrow="__('index.testimonials_eyebrow')" :title="__('index.testimonials_title')" />
            </div>
            <div class="container-site mt-14" data-reveal>
                <div class="swiper overflow-visible!" data-swiper>
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $testimonial)
                            <figure class="swiper-slide h-auto! rounded-3xl border border-ink-900/5 bg-white p-8">
                                <svg viewBox="0 0 24 24" class="size-8 text-brand-500" fill="currentColor" aria-hidden="true"><path d="M7 7h4v4c0 3-1.5 5-4 6l-.8-1.4C7.6 14.8 8.2 13.6 8.3 12H7V7Zm8 0h4v4c0 3-1.5 5-4 6l-.8-1.4c1.4-.8 2-2 2.1-3.6H15V7Z" /></svg>
                                <blockquote class="mt-5 leading-relaxed text-ink-700">{{ $testimonial->tr('quote') }}</blockquote>
                                <figcaption class="mt-8 flex items-center gap-4">
                                    @if ($testimonial->avatar)
                                        <img src="{{ Storage::disk('public')->url($testimonial->avatar) }}" alt="" class="size-12 rounded-full object-cover" loading="lazy">
                                    @else
                                        <span class="inline-flex size-12 items-center justify-center rounded-full bg-brand-700 font-bold text-white">{{ mb_substr($testimonial->name, 0, 1) }}</span>
                                    @endif
                                    <span>
                                        <span class="block font-bold">{{ $testimonial->name }}</span>
                                        <span class="block text-sm text-ink-700">{{ $testimonial->tr('position') }}</span>
                                    </span>
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Careers (the "Become a hero" band of the Drovox home page) --}}
    <section id="team" class="pb-24 sm:pb-32">
        <div class="container-site">
            <div class="relative isolate overflow-hidden rounded-[2rem] bg-ink-950 px-8 py-14 text-white sm:px-14 sm:py-20" data-reveal>
                <div class="grid-lines absolute inset-0 -z-10" aria-hidden="true"></div>
                <div class="absolute -end-20 -top-20 -z-10 size-96 rounded-full bg-brand-600/40 blur-[100px]" aria-hidden="true"></div>
                <div class="grid items-center gap-10 lg:grid-cols-2">
                    <div>
                        <p class="eyebrow text-brand-400!">{{ __('index.careers_eyebrow') }}</p>
                        <h2 class="mt-4 text-3xl font-extrabold leading-tight sm:text-4xl">{{ __('index.careers_title') }}</h2>
                        <p class="mt-5 text-lg leading-relaxed text-white/70">{{ __('index.careers_text') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3 lg:justify-end">
                        @if ($featuredOpening)
                            <x-ui.button :href="route('careers.show', $featuredOpening)" variant="light">{{ __('global.actions.apply_now') }}</x-ui.button>
                        @endif
                        <x-ui.button :href="route('careers.index')" variant="outline-light">{{ __('global.actions.all_openings') }}</x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ preview --}}
    @if ($faqs->isNotEmpty())
        <section id="faqs" class="bg-white py-24 sm:py-32">
            <div class="container-site grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <x-ui.section-heading :eyebrow="__('index.faq_eyebrow')" :title="__('index.faq_title')" />
                    <x-ui.button :href="route('faqs')" variant="ghost" class="mt-6" data-reveal>{{ __('faqs.title') }}</x-ui.button>
                </div>
                <div class="lg:col-span-8" data-reveal>
                    <x-ui.faq-list :faqs="$faqs" />
                </div>
            </div>
        </section>
    @endif

    <x-ui.cta-band :title="__('index.cta_title')" :text="__('index.cta_text')">
        <x-ui.button :href="route('request-service')" variant="light">{{ __('header.request_service') }}</x-ui.button>
        <x-ui.button :href="route('contact')" variant="outline-light">{{ __('global.actions.lets_talk') }}</x-ui.button>
    </x-ui.cta-band>
@endsection
