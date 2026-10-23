@extends('layouts.app')

@section('title', __('index.meta_title'))

@section('content')
    {{-- Hero: cinematic, deep royal green, star lattice over a brand photo (or the one uploaded in Site settings). --}}
    <section id="home" class="relative isolate flex min-h-dvh flex-col overflow-hidden bg-forest-950 text-white">
        <img src="{{ ($heroImage = $site->get('hero_image')) ? Storage::disk('public')->url($heroImage) : asset(config('site.images.hero')) }}" alt="" class="ken-burns absolute inset-0 -z-20 size-full object-cover opacity-50">
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-forest-950 via-forest-950/60 to-forest-950/40" aria-hidden="true"></div>
        <div class="pattern-star absolute inset-0 -z-10 text-white/[0.03]" aria-hidden="true"></div>

        <div class="container-site flex flex-1 items-center pt-40 pb-16">
            <div class="max-w-5xl">
                <p class="eyebrow text-gold-400!" data-reveal>{{ __('global.brand_tagline') }}</p>
                <h1 class="heading-display mt-10 text-[2.75rem] leading-[1.08] sm:text-7xl lg:text-[5.5rem]" data-split>
                    @foreach (preg_split('/[،,]\s*/u', $site->tr('hero_title')) as $line)
                        <span @class(['block', 'text-gold-300' => $loop->iteration === 2])>{{ $line }}</span>
                    @endforeach
                </h1>
                <p class="mt-10 max-w-2xl text-lg leading-[1.85] text-white/70 text-pretty" data-reveal data-reveal-delay="0.4">
                    {{ $site->tr('hero_subtitle') }}
                </p>
                <div class="mt-12 flex flex-wrap gap-3" data-reveal data-reveal-delay="0.5">
                    <x-ui.button :href="route('request-service')" variant="gold">{{ __('header.request_service') }}</x-ui.button>
                    <x-ui.button :href="route('saudi-gate')" variant="outline-light">{{ __('global.actions.explore_gate') }}</x-ui.button>
                </div>
            </div>
        </div>

        {{-- Featured strip along the bottom of the hero, as on the big consulting firms' home pages. --}}
        <div class="relative border-t border-white/10 bg-forest-950/40 backdrop-blur-sm">
            <div class="container-site grid divide-y divide-white/10 md:grid-cols-3 md:divide-x md:divide-y-0 rtl:md:divide-x-reverse">
                @php
                    $highlights = array_filter([
                        $insights->first() ? ['label' => $insights->first()->type->getLabel(), 'title' => $insights->first()->tr('title'), 'url' => route('insights.show', $insights->first())] : null,
                        ['label' => __('header.saudi_gate'), 'title' => __('index.gate_title'), 'url' => route('saudi-gate')],
                        $featuredOpening ? ['label' => __('header.careers'), 'title' => $featuredOpening->tr('title'), 'url' => route('careers.show', $featuredOpening)] : ['label' => __('header.services'), 'title' => __('index.services_title'), 'url' => route('services.index')],
                    ]);
                @endphp
                @foreach ($highlights as $highlight)
                    <a href="{{ $highlight['url'] }}" class="group flex items-start gap-5 py-7 md:px-7 md:first:ps-0 md:last:pe-0" data-reveal data-reveal-delay="{{ 0.6 + $loop->index * 0.1 }}">
                        <span class="mt-1 font-latin text-xs text-gold-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="flex-1">
                            <span class="block text-xs tracking-wide text-white/50">{{ $highlight['label'] }}</span>
                            <span class="mt-2 line-clamp-2 block leading-snug text-white transition group-hover:text-gold-300">{{ $highlight['title'] }}</span>
                        </span>
                        <x-ui.icon name="arrow" class="mt-1 size-4 text-white/30 transition group-hover:translate-x-1 group-hover:text-gold-300 rtl:-scale-x-100 rtl:group-hover:-translate-x-1" />
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 01 Intro statement --}}
    <section id="intro" class="py-28 sm:py-40">
        <div class="container-site grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="eyebrow" data-reveal><span class="font-latin">01</span> {{ __('index.intro_eyebrow') }}</p>
                <div class="bracket-frame mt-12 hidden max-w-xs lg:block">
                <div class="relative aspect-[3/4] overflow-hidden bg-forest-900" data-reveal-image>
                    <img src="{{ ($aboutImage = $site->get('about_image')) ? Storage::disk('public')->url($aboutImage) : asset(config('site.images.intro')) }}" alt="" loading="lazy" class="size-full object-cover">
                </div>
                </div>
            </div>
            <div class="lg:col-span-8">
                <h2 class="heading-display text-4xl leading-[1.2] sm:text-5xl lg:text-6xl" data-split>{{ __('index.intro_title') }}</h2>
                <div class="mt-12 grid gap-8 text-lg leading-[1.9] text-ink-700 md:grid-cols-2">
                    <p data-reveal>{{ $site->tr('home_intro') }}</p>
                    <p data-reveal data-reveal-delay="0.1">{{ $site->tr('about_team') }}</p>
                </div>
                <div class="mt-12 flex flex-wrap items-center gap-8" data-reveal>
                    <x-ui.button :href="route('about')" variant="outline">{{ __('global.actions.learn_more') }}</x-ui.button>
                    <span class="inline-flex items-center gap-3 text-sm text-gold-600">
                        <x-ui.icon name="star" class="size-4" /> {{ __('index.customize_to_fit') }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Facts & figures (the "أرقام وإحصائيات" band of the Royal Commission sites) --}}
    @if ($stats = $site->get('home_stats'))
        <section id="counters" class="relative overflow-hidden bg-sand-100 py-20 sm:py-24">
            <div class="pattern-star absolute inset-0 text-gold-500/10" aria-hidden="true"></div>
            <div class="container-site relative">
                <p class="eyebrow" data-reveal>{{ __('index.stats_eyebrow') }}</p>
                <div class="mt-12 grid gap-y-12 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($stats as $stat)
                        @php(preg_match('/^(\D*)(\d+)(\D*)$/u', (string) $stat['value'], $parts))
                        <div @class(['lg:px-8', 'lg:border-s lg:border-gold-500/30' => ! $loop->first, 'lg:ps-0' => $loop->first]) data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                            <span class="inline-flex size-12 items-center justify-center rounded-full border border-gold-500/40 text-gold-600">
                                <x-ui.icon :name="($stat['icon'] ?? null) ?: ['star', 'briefcase', 'network', 'compass'][$loop->index % 4]" class="size-5" />
                            </span>
                            <p class="mt-6 font-display text-6xl font-light tracking-tight text-forest-900" dir="ltr">
                                {{-- Animate counts, but not years like "2019". --}}
                                @if ($parts && (int) $parts[2] < 1000)
                                    {{ $parts[1] }}<span data-counter="{{ $parts[2] }}">{{ $parts[2] }}</span>{{ $parts[3] }}
                                @else
                                    {{ $stat['value'] }}
                                @endif
                            </p>
                            <p class="mt-3 max-w-56 leading-relaxed text-ink-700">{{ App\Support\Translatable::pick($stat['label'] ?? null) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 02 How we help: editorial capability list --}}
    <section id="services" class="py-28 sm:py-40">
        <div class="container-site">
            <div class="flex flex-col justify-between gap-10 lg:flex-row lg:items-end">
                <x-ui.section-heading number="02" :eyebrow="__('index.capabilities_eyebrow')" :title="__('index.capabilities_title')" />
                <x-ui.button :href="route('services.index')" variant="outline" class="shrink-0" data-reveal>{{ __('global.actions.all_services') }}</x-ui.button>
            </div>

            <ol class="mt-20 border-t border-ink-900/10">
                @foreach ($services as $service)
                    <li class="border-b border-ink-900/10" data-reveal>
                        <a href="{{ route('services.show', $service) }}" class="group grid items-center gap-6 py-10 transition-colors duration-500 hover:bg-white md:grid-cols-12 md:px-6">
                            <span class="font-latin text-sm text-gold-600 md:col-span-1">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="heading-display text-3xl transition group-hover:text-brand-700 md:col-span-5 lg:text-4xl">{{ $service->tr('title') }}</h3>
                            <p class="leading-relaxed text-ink-700 md:col-span-5">{{ $service->tr('excerpt') }}</p>
                            <span class="hidden justify-end md:col-span-1 md:flex">
                                <span class="inline-flex size-12 items-center justify-center rounded-full border border-ink-900/15 transition duration-500 group-hover:border-brand-700 group-hover:bg-brand-700 group-hover:text-white">
                                    <x-ui.icon name="arrow" class="size-4 rtl:-scale-x-100" />
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- 03 Saudi Gate --}}
    <section id="saudi-gate" class="relative overflow-hidden bg-forest-950 py-28 text-white sm:py-40">
        <div class="pattern-star absolute inset-0 text-gold-300/[0.05]" aria-hidden="true"></div>
        <div class="absolute -end-60 top-0 size-[40rem] rounded-full bg-brand-700/20 blur-[140px]" aria-hidden="true"></div>
        <div class="container-site relative">
            <div class="flex flex-col justify-between gap-10 lg:flex-row lg:items-end">
                <x-ui.section-heading dark number="03" :eyebrow="__('index.gate_eyebrow')" :title="__('index.gate_title')" :text="__('index.gate_text')" />
                <x-ui.button :href="route('saudi-gate')" variant="gold" class="shrink-0" data-reveal>{{ __('global.actions.explore_gate') }}</x-ui.button>
            </div>
        </div>

        <div class="container-site relative mt-20" data-reveal>
            <div class="swiper overflow-visible!" data-swiper>
                <div class="swiper-wrapper">
                    @foreach ($packages as $package)
                        <div class="swiper-slide h-auto!">
                            <a href="{{ route('saudi-gate') }}#{{ $package->slug }}" @class([
                                'group flex h-full min-h-72 flex-col border p-8 transition duration-700 hover:-translate-y-1',
                                'border-gold-500/50 bg-gradient-to-b from-gold-500/15 to-transparent' => $package->is_premium,
                                'border-white/10 bg-white/[0.02] hover:border-gold-500/40' => ! $package->is_premium,
                            ])>
                                <div class="flex items-center justify-between">
                                    <span class="font-latin text-sm text-gold-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    @if ($package->is_premium)
                                        <span class="border border-gold-400/60 px-2.5 py-1 text-[0.65rem] tracking-wider text-gold-300">{{ __('gate.premium') }}</span>
                                    @endif
                                </div>
                                <h3 class="heading-display mt-auto pt-16 text-4xl">{{ $package->tr('title') }}</h3>
                                <p class="mt-3 leading-relaxed text-white/60">{{ $package->tr('tagline') }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- 04 Impact (shown once case studies are published) --}}
    @if ($caseStudies->isNotEmpty())
        <section id="impact" class="py-28 sm:py-40">
            <div class="container-site">
                <div class="flex flex-col justify-between gap-10 lg:flex-row lg:items-end">
                    <x-ui.section-heading number="04" :eyebrow="__('impact.eyebrow')" :title="__('impact.home_title')" />
                    <x-ui.button :href="route('impact.index')" variant="outline" class="shrink-0" data-reveal>{{ __('header.impact') }}</x-ui.button>
                </div>
                <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($caseStudies as $caseStudy)
                        <x-ui.case-study-card :case-study="$caseStudy" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Insights: featured story plus the latest pieces --}}
    @if ($insights->isNotEmpty())
        <section id="insights" @class(['py-28 sm:py-40', 'bg-white' => $caseStudies->isNotEmpty()])>
            <div class="container-site">
                <div class="flex flex-col justify-between gap-10 lg:flex-row lg:items-end">
                    <x-ui.section-heading :number="$caseStudies->isNotEmpty() ? '05' : '04'" :eyebrow="__('index.insights_eyebrow')" :title="__('index.insights_title')" />
                    <x-ui.button :href="route('insights.index')" variant="outline" class="shrink-0" data-reveal>{{ __('insights.view_all') }}</x-ui.button>
                </div>

                <div class="mt-16 grid gap-x-10 gap-y-14 lg:grid-cols-12">
                    @php($lead = $insights->first())
                    <article class="group relative lg:col-span-7" data-reveal>
                        <x-ui.insight-cover :insight="$lead" class="aspect-[16/10]" />
                        <p class="mt-7 text-xs text-ink-700/70">{{ $lead->published_at->locale(app()->getLocale())->translatedFormat('j F Y') }} · {{ __('insights.reading_time', ['minutes' => $lead->readingMinutes()]) }}</p>
                        <h3 class="heading-display mt-3 text-3xl leading-snug transition group-hover:text-brand-700 sm:text-4xl">
                            <a href="{{ route('insights.show', $lead) }}" class="after:absolute after:inset-0">{{ $lead->tr('title') }}</a>
                        </h3>
                        <p class="mt-4 max-w-2xl text-lg leading-relaxed text-ink-700">{{ $lead->tr('excerpt') }}</p>
                    </article>
                    <div class="space-y-12 lg:col-span-5">
                        @foreach ($insights->skip(1) as $insight)
                            <article class="group relative grid grid-cols-5 gap-6" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
                                <x-ui.insight-cover :insight="$insight" class="col-span-2 aspect-square" />
                                <div class="col-span-3">
                                    <p class="text-xs text-ink-700/70">{{ __('insights.reading_time', ['minutes' => $insight->readingMinutes()]) }}</p>
                                    <h3 class="heading-display mt-2 text-xl leading-snug transition group-hover:text-brand-700">
                                        <a href="{{ route('insights.show', $insight) }}" class="after:absolute after:inset-0">{{ $insight->tr('title') }}</a>
                                    </h3>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Customize to Fit --}}
    <section id="steps" class="relative overflow-hidden bg-forest-900 py-28 text-white sm:py-36">
        <div class="pattern-star absolute inset-0 text-white/[0.03]" aria-hidden="true"></div>
        <div class="container-site relative">
            <x-ui.section-heading dark align="center" :eyebrow="__('index.method_eyebrow')" :title="__('index.method_title')" :text="__('index.method_text')" />

            <ol class="mt-20 grid gap-px bg-white/10 md:grid-cols-2 lg:grid-cols-4">
                @foreach (__('index.method') as $step)
                    <li class="bg-forest-900 p-8 lg:p-10" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
                        <span class="font-display text-5xl font-light text-gold-400">{{ $loop->iteration }}</span>
                        <h3 class="mt-8 text-xl">{{ $step['title'] }}</h3>
                        <p class="mt-3 leading-relaxed text-white/60">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Who we serve --}}
    <section id="overview" class="py-28 sm:py-40">
        <div class="container-site">
            <x-ui.section-heading align="center" :eyebrow="__('index.audience_eyebrow')" :title="__('index.audience_title')" />

            <div class="mt-20 grid gap-px bg-ink-900/10 lg:grid-cols-3">
                @foreach (__('index.audiences') as $audience)
                    <div class="bg-paper p-10 transition duration-500 hover:bg-white" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}">
                        <span class="inline-flex size-14 items-center justify-center rounded-full border border-gold-500/40 text-brand-700">
                            <x-ui.icon :name="['building', 'briefcase', 'rocket'][$loop->index] ?? 'star'" />
                        </span>
                        <h3 class="heading-display mt-8 text-2xl">{{ $audience['title'] }}</h3>
                        <p class="mt-4 leading-relaxed text-ink-700">{{ $audience['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Partners --}}
    @if ($partners->isNotEmpty())
        <section class="border-y border-ink-900/10 bg-white py-14" aria-label="{{ __('index.partners_title') }}">
            <div class="container-site">
                <p class="text-center text-xs tracking-[0.2em] text-ink-700/60 uppercase rtl:tracking-normal">{{ __('index.partners_title') }}</p>
                <div class="mt-10 flex flex-wrap items-center justify-center gap-x-16 gap-y-10">
                    @foreach ($partners as $partner)
                        @php($logo = '<img src="'.e(Storage::disk('public')->url($partner->logo)).'" alt="'.e($partner->name).'" class="h-10 w-auto opacity-50 grayscale transition duration-500 hover:opacity-100 hover:grayscale-0" loading="lazy">')
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

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section id="testimonials" class="overflow-hidden bg-sand-50 py-28 sm:py-36">
            <div class="container-site">
                <x-ui.section-heading :eyebrow="__('index.testimonials_eyebrow')" :title="__('index.testimonials_title')" />
            </div>
            <div class="container-site mt-16" data-reveal>
                <div class="swiper overflow-visible!" data-swiper>
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $testimonial)
                            <figure class="swiper-slide h-auto! border border-ink-900/10 bg-white p-10">
                                <span class="font-display text-7xl leading-none text-gold-400" aria-hidden="true">&ldquo;</span>
                                <blockquote class="heading-display text-xl leading-relaxed text-ink-900">{{ $testimonial->tr('quote') }}</blockquote>
                                <figcaption class="mt-10 flex items-center gap-4 border-t border-ink-900/10 pt-6">
                                    @if ($testimonial->avatar)
                                        <img src="{{ Storage::disk('public')->url($testimonial->avatar) }}" alt="" class="size-12 rounded-full object-cover" loading="lazy">
                                    @endif
                                    <span>
                                        <span class="block font-medium">{{ $testimonial->name }}</span>
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

    {{-- Careers --}}
    <section id="team" class="relative isolate overflow-hidden bg-forest-950 py-28 text-white sm:py-36">
        <div class="pattern-star absolute inset-0 -z-10 text-gold-300/[0.05]" aria-hidden="true"></div>
        <div class="container-site grid items-end gap-12 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <x-ui.section-heading dark :eyebrow="__('index.careers_eyebrow')" :title="__('index.careers_title')" :text="__('index.careers_text')" />
            </div>
            <div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end" data-reveal>
                @if ($featuredOpening)
                    <x-ui.button :href="route('careers.show', $featuredOpening)" variant="gold">{{ __('global.actions.apply_now') }}</x-ui.button>
                @endif
                <x-ui.button :href="route('careers.index')" variant="outline-light">{{ __('global.actions.all_openings') }}</x-ui.button>
            </div>
        </div>
    </section>

    {{-- FAQ preview --}}
    @if ($faqs->isNotEmpty())
        <section id="faqs" class="py-28 sm:py-36">
            <div class="container-site grid gap-16 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <x-ui.section-heading :eyebrow="__('index.faq_eyebrow')" :title="__('index.faq_title')" />
                    <x-ui.button :href="route('faqs')" variant="ghost" class="mt-8" data-reveal>{{ __('faqs.title') }}</x-ui.button>
                </div>
                <div class="lg:col-span-8" data-reveal>
                    <x-ui.faq-list :faqs="$faqs" />
                </div>
            </div>
        </section>
    @endif

    <x-ui.cta-band :title="__('index.cta_title')" :text="__('index.cta_text')">
        <x-ui.button :href="route('request-service')" variant="gold">{{ __('header.request_service') }}</x-ui.button>
        <x-ui.button :href="route('contact')" variant="outline-light">{{ __('global.actions.lets_talk') }}</x-ui.button>
    </x-ui.cta-band>
@endsection
