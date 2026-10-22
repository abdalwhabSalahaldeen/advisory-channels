@props(['services', 'packages'])

@php
    // "I am … and I need …": each audience sees the needs that apply to it, and each need points at a page.
    $serviceNeeds = ['strategy' => 'strategy-growth', 'organization' => 'organizational-development', 'transformation' => 'transformation-management', 'outsourcing' => 'business-process-outsourcing'];
    $gateNeeds = ['assess' => 'assess', 'start' => 'start', 'operate' => 'operate', 'grow' => 'grow'];

    $results = [];

    foreach ($serviceNeeds as $need => $slug) {
        if ($service = $services->firstWhere('slug', $slug)) {
            $results[$need] = [
                'title' => $service->tr('title'),
                'text' => $service->tr('excerpt'),
                'url' => route('services.show', $service),
                'cta' => __('index.picker.cta_service'),
                'ctaUrl' => route('request-service', ['service' => $slug]),
            ];
        }
    }

    foreach ($gateNeeds as $need => $slug) {
        if ($package = $packages->firstWhere('slug', $slug)) {
            $results[$need] = [
                'title' => __('header.saudi_gate').' · '.$package->tr('title'),
                'text' => $package->tr('description'),
                'url' => route('saudi-gate').'#'.$slug,
                'cta' => __('index.picker.cta_gate'),
                'ctaUrl' => route('saudi-gate').'#gate-form',
            ];
        }
    }

    $audiences = [
        'government' => array_keys($serviceNeeds),
        'corporate' => array_keys($serviceNeeds),
        'investor' => [...array_keys($gateNeeds), 'strategy'],
    ];

    $audiences = array_map(fn ($needs) => array_values(array_filter($needs, fn ($need) => isset($results[$need]))), $audiences);
    $needLabels = collect(__('index.picker.needs'))->only(array_keys($results))->all();
@endphp

<section
    id="start"
    class="relative overflow-hidden border-b border-gold-500/20 bg-sand-50 py-24 sm:py-32"
    x-data="{ audience: '', need: '', audiences: @js($audiences), results: @js($results), labels: @js($needLabels) }"
    x-effect="if (audience && ! audiences[audience].includes(need)) need = ''"
>
    <div class="pattern-star absolute inset-0 text-gold-500/[0.07]" aria-hidden="true"></div>

    <div class="container-site relative">
        <p class="eyebrow" data-reveal>{{ __('index.picker.eyebrow') }}</p>
        <h2 class="sr-only">{{ __('index.picker.title') }}</h2>

        <div class="heading-display mt-10 text-3xl leading-[1.7] sm:text-4xl lg:text-5xl lg:leading-[1.6]" data-reveal>
            <label for="picker-audience">{{ __('index.picker.i_am') }}</label>
            <select id="picker-audience" x-model="audience" class="mx-2 max-w-full cursor-pointer appearance-none border-0 border-b border-gold-500 bg-transparent px-1 pb-1 font-display text-brand-700 focus:border-brand-700 focus:ring-0 focus:outline-none">
                <option value="">{{ __('index.picker.choose') }}…</option>
                @foreach (__('index.picker.audiences') as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <br class="hidden lg:block">
            <label for="picker-need">{{ __('index.picker.i_need') }}</label>
            <select id="picker-need" x-model="need" :disabled="! audience" class="mx-2 max-w-full cursor-pointer appearance-none border-0 border-b border-gold-500 bg-transparent px-1 pb-1 font-display text-brand-700 focus:border-brand-700 focus:ring-0 focus:outline-none disabled:cursor-not-allowed disabled:opacity-40">
                <option value="">{{ __('index.picker.choose') }}…</option>
                <template x-for="key in (audiences[audience] ?? [])" :key="key">
                    <option :value="key" x-text="labels[key]"></option>
                </template>
            </select>
        </div>

        <div
            x-show="need && results[need]"
            x-cloak
            x-transition:enter="transition duration-700 ease-out-expo"
            x-transition:enter-start="opacity-0 translate-y-4"
            class="mt-14 grid items-end gap-8 border-t border-gold-500/30 pt-10 lg:grid-cols-12"
            aria-live="polite"
        >
            <div class="lg:col-span-8">
                <p class="text-sm text-gold-600">{{ __('index.picker.result') }}</p>
                <p class="heading-display mt-3 text-3xl" x-text="results[need]?.title"></p>
                <p class="mt-4 max-w-2xl leading-relaxed text-ink-700" x-text="results[need]?.text"></p>
            </div>
            <div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end">
                <a :href="results[need]?.ctaUrl" class="group inline-flex items-center gap-4 rounded-sm bg-brand-700 px-7 py-4 text-sm font-medium tracking-wide text-white transition hover:bg-brand-800">
                    <span x-text="results[need]?.cta"></span>
                    <x-ui.icon name="arrow" class="size-4 transition-transform duration-500 group-hover:translate-x-1.5 rtl:-scale-x-100 rtl:group-hover:-translate-x-1.5" />
                </a>
                <a :href="results[need]?.url" class="inline-flex items-center px-2 py-4 text-sm text-brand-700 underline decoration-gold-400 underline-offset-4">{{ __('index.picker.learn_more') }}</a>
            </div>
        </div>
    </div>
</section>
