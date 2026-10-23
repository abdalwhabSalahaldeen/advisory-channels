@extends('layouts.app')

@section('title', __('gate.meta_title'))
@section('description', __('gate.meta_description'))

@section('content')
    <x-ui.page-hero :image="asset(config('site.images.pages.saudi-gate'))" :eyebrow="__('gate.eyebrow')" :title="__('gate.title')" :text="__('gate.text')">
        <div class="mt-10 flex flex-wrap gap-3">
            <x-ui.button href="#gate-form">{{ __('gate.form_title') }}</x-ui.button>
            <x-ui.button href="#journey" variant="outline-light" icon="arrow-down">{{ __('gate.journey_title') }}</x-ui.button>
        </div>
    </x-ui.page-hero>

    {{-- Saudi Arabia --}}
    <section class="py-24 sm:py-32">
        <div class="container-site grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <x-ui.section-heading :eyebrow="__('gate.ksa_eyebrow')" :title="__('gate.ksa_title')" />
                <p class="mt-10 font-display text-2xl font-light text-brand-700 rtl:font-arabic" data-reveal>{{ __('gate.ksa_motto') }}</p>
            </div>
            <div class="lg:col-span-7">
                <p class="text-lg leading-relaxed text-ink-700" data-reveal>{{ __('gate.ksa_text') }}</p>
                <dl class="mt-12 grid gap-5 sm:grid-cols-3">
                    @foreach (__('gate.ksa_facts') as $fact)
                        <div class="flex flex-col rounded-sm border border-ink-900/5 bg-white p-6" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                            <dt class="order-2 mt-2 text-sm font-semibold text-ink-700">{{ $fact['label'] }}</dt>
                            <dd class="font-display text-4xl font-light text-brand-700" dir="ltr">
                                {{ $fact['value'] }}
                                @isset($fact['suffix'])
                                    <span class="block font-sans text-base font-medium rtl:font-arabic">{{ $fact['suffix'] }}</span>
                                @endisset
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- Vision 2030 --}}
    <section class="relative overflow-hidden bg-forest-950 py-24 text-white sm:py-32">
        <div class="pattern-star absolute inset-0 text-white/[0.03]" aria-hidden="true"></div>
        <div class="absolute -start-40 top-1/2 size-[36rem] -translate-y-1/2 rounded-full bg-brand-700/30 blur-[120px]" aria-hidden="true"></div>
        <div class="container-site relative grid gap-16 lg:grid-cols-2 lg:items-center">
            <x-ui.section-heading dark :eyebrow="__('gate.vision_eyebrow')" :title="__('gate.vision_title')" :text="__('gate.vision_text')" />
            <blockquote class="bracket-frame p-10 text-3xl leading-snug font-medium text-balance sm:text-4xl" data-reveal data-reveal-delay="0.1">
                {{ __('gate.vision_quote') }}
            </blockquote>
        </div>
    </section>

    {{-- Investment --}}
    <section class="py-24 sm:py-32">
        <div class="container-site grid gap-16 lg:grid-cols-2 lg:items-center">
            <x-ui.section-heading :eyebrow="__('gate.invest_eyebrow')" :title="__('gate.invest_title')" :text="__('gate.invest_text')" />
            <ul class="grid gap-4 sm:grid-cols-2">
                @foreach (__('gate.invest_points') as $point)
                    <li class="flex flex-col gap-5 rounded-sm bg-white p-7 ring-1 ring-ink-900/5" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-brand-700 text-white">
                            <x-ui.icon name="check" class="size-5" />
                        </span>
                        <span class="text-lg font-medium leading-snug">{{ $point }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- The journey --}}
    <section id="journey" class="bg-white py-24 sm:py-32">
        <div class="container-site">
            <x-ui.section-heading align="center" :eyebrow="__('gate.journey_eyebrow')" :title="__('gate.journey_title')" :text="__('gate.journey_text')" />

            <div class="relative mx-auto mt-20 max-w-4xl" data-journey>
                {{-- Track + progress bar that fills while scrolling (resources/js/app.js). --}}
                <div class="absolute start-7 top-0 bottom-0 w-px bg-ink-900/10 sm:start-1/2" aria-hidden="true">
                    <div class="h-full origin-top bg-brand-600" data-journey-bar></div>
                </div>

                <ol class="relative">
                    @foreach ($packages as $package)
                        <li id="{{ $package->slug }}" class="group relative grid scroll-mt-28 gap-6 pb-16 ps-20 last:pb-0 sm:grid-cols-2 sm:gap-16 sm:ps-0" data-journey-step>
                            <span class="absolute start-0 top-0 z-10 inline-flex size-14 items-center justify-center rounded-full border-4 border-white bg-ink-900 font-latin text-lg font-medium text-white shadow-lg transition duration-500 group-[.is-active]:bg-brand-700 sm:start-1/2 sm:-translate-x-1/2 rtl:sm:translate-x-1/2">
                                {{ $loop->iteration }}
                            </span>

                            <div @class(['sm:pt-3', 'sm:order-2' => $loop->even, 'sm:text-end' => $loop->odd])>
                                <p class="font-latin text-sm font-medium text-brand-700">{{ __('gate.stage') }} {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                <h3 class="mt-2 heading-display text-4xl">
                                    {{ $package->tr('title') }}
                                    @if ($package->is_premium)
                                        <span class="ms-2 inline-block rounded-full bg-sand-500 px-3 py-1 align-middle text-xs font-medium text-white">{{ __('gate.premium') }}</span>
                                    @endif
                                </h3>
                                <p class="mt-3 text-lg font-semibold text-ink-700">{{ $package->tr('tagline') }}</p>
                            </div>

                            <div @class([
                                'rounded-sm border p-7 transition duration-500',
                                'border-sand-500/40 bg-sand-100' => $package->is_premium,
                                'border-ink-900/5 bg-paper group-[.is-active]:border-brand-500/30 group-[.is-active]:bg-brand-50' => ! $package->is_premium,
                                'sm:order-1' => $loop->even,
                            ])>
                                <p class="leading-relaxed text-ink-700">{{ $package->tr('description') }}</p>
                                @if ($deliverables = $package->trList('deliverables'))
                                    <p class="mt-6 text-sm font-medium text-ink-900">{{ __('gate.includes') }}</p>
                                    <ul class="mt-3 flex flex-wrap gap-2">
                                        @foreach ($deliverables as $deliverable)
                                            <li class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-ink-700 ring-1 ring-ink-900/5">{{ $deliverable }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <p class="mx-auto mt-20 max-w-3xl text-center heading-display text-3xl leading-snug text-balance text-brand-700 sm:text-3xl" data-reveal>
                {{ __('gate.closing') }}
            </p>
        </div>
    </section>

    {{-- FAQs --}}
    @if ($faqs->isNotEmpty())
        <section class="py-24 sm:py-32">
            <div class="container-site grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <x-ui.section-heading :title="__('gate.faq_title')" />
                </div>
                <div class="lg:col-span-8" data-reveal>
                    <x-ui.faq-list :faqs="$faqs" />
                </div>
            </div>
        </section>
    @endif

    {{-- Form --}}
    <section id="gate-form" class="relative scroll-mt-20 overflow-hidden bg-forest-950 py-24 text-white sm:py-32">
        <div class="pattern-star absolute inset-0 text-white/[0.03]" aria-hidden="true"></div>
        <div class="container-site relative grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <x-ui.section-heading dark :eyebrow="__('gate.form_eyebrow')" :title="__('gate.form_title')" :text="__('gate.form_text')" />
            </div>
            <div class="lg:col-span-7" data-reveal>
                <div class="rounded-sm bg-white p-6 text-ink-900 sm:p-10">
                    <livewire:saudi-gate-form />
                </div>
            </div>
        </div>
    </section>
@endsection
