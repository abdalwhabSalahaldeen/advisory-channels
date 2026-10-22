@extends('layouts.app')

@section('title', $caseStudy->tr('title'))
@section('description', $caseStudy->tr('summary'))

@section('content')
    <x-ui.page-hero
        :eyebrow="__('impact.eyebrow')"
        :title="$caseStudy->tr('title')"
        :text="$caseStudy->tr('summary')"
        :image="$caseStudy->cover ? Storage::disk('public')->url($caseStudy->cover) : null"
    />

    {{-- Facts strip --}}
    <section class="border-b border-ink-900/10 bg-white">
        <dl class="container-site grid divide-y divide-ink-900/10 sm:grid-cols-3 sm:divide-x sm:divide-y-0 rtl:sm:divide-x-reverse">
            @foreach (['client' => $caseStudy->tr('client'), 'sector' => $caseStudy->tr('sector'), 'service' => $caseStudy->service?->tr('title')] as $label => $value)
                @continue(blank($value))
                <div class="px-2 py-8 sm:px-8">
                    <dt class="text-xs tracking-wide text-ink-700/60">{{ __("impact.{$label}") }}</dt>
                    <dd class="mt-2 text-lg">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- Metrics --}}
    @if ($metrics = $caseStudy->metrics)
        <section class="bg-forest-950 py-20 text-white">
            <div class="container-site grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($metrics as $metric)
                    <div class="border-s border-gold-500/40 ps-6" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                        <p class="font-display text-6xl font-light text-gold-300" dir="ltr">{{ $metric['value'] }}</p>
                        <p class="mt-3 text-white/70">{{ App\Support\Translatable::pick($metric['label'] ?? null) }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="py-20 sm:py-28">
        <div class="container-site space-y-20">
            @foreach (['challenge', 'approach', 'outcome'] as $part)
                @continue(blank($caseStudy->tr($part)))
                <div class="grid gap-8 lg:grid-cols-12">
                    <h2 class="eyebrow lg:col-span-3 lg:pt-3" data-reveal>
                        <span class="font-latin">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        {{ __("impact.{$part}") }}
                    </h2>
                    <p class="text-xl leading-[1.9] whitespace-pre-line text-ink-700 lg:col-span-8" data-reveal>{{ $caseStudy->tr($part) }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @if ($others->isNotEmpty())
        <section class="bg-sand-50 py-20 sm:py-28">
            <div class="container-site">
                <x-ui.section-heading :title="__('impact.more')" />
                <div class="mt-14 grid gap-8 md:grid-cols-2">
                    @foreach ($others as $other)
                        <x-ui.case-study-card :case-study="$other" data-reveal />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-ui.cta-band :title="__('index.cta_title')" :text="__('index.cta_text')">
        <x-ui.button :href="route('request-service')" variant="gold">{{ __('header.request_service') }}</x-ui.button>
    </x-ui.cta-band>
@endsection
