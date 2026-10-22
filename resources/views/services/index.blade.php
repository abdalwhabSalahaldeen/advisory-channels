@extends('layouts.app')

@section('title', __('services.meta_title'))
@section('description', __('services.text'))

@section('content')
    <x-ui.page-hero :eyebrow="__('services.eyebrow')" :title="__('services.title')" :text="__('services.text')" />

    <section class="py-24 sm:py-32">
        <div class="container-site space-y-5">
            @foreach ($services as $service)
                <a href="{{ route('services.show', $service) }}" class="card-lift group grid gap-8 rounded-3xl border border-ink-900/5 bg-white p-8 sm:p-12 lg:grid-cols-12 lg:items-center" data-reveal>
                    <div class="flex items-center gap-6 lg:col-span-5">
                        <span class="font-latin text-5xl font-extrabold text-brand-700/20 transition group-hover:text-brand-700">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <span class="inline-flex size-12 items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                                <x-ui.icon :name="$service->icon ?? 'compass'" />
                            </span>
                            <h2 class="mt-4 text-2xl font-extrabold sm:text-3xl">{{ $service->tr('title') }}</h2>
                        </div>
                    </div>
                    <div class="lg:col-span-6">
                        <p class="text-lg leading-relaxed text-ink-700">{{ $service->tr('excerpt') }}</p>
                        <ul class="mt-5 flex flex-wrap gap-2">
                            @foreach ($service->trList('highlights') as $highlight)
                                <li class="rounded-full bg-paper px-3 py-1.5 text-xs font-semibold text-ink-700">{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="flex lg:col-span-1 lg:justify-end">
                        <span class="inline-flex size-14 items-center justify-center rounded-full border border-ink-900/10 transition group-hover:border-brand-700 group-hover:bg-brand-700 group-hover:text-white">
                            <x-ui.icon name="arrow" class="size-5 rtl:-scale-x-100" />
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <x-ui.cta-band :title="__('index.cta_title')" :text="__('index.cta_text')">
        <x-ui.button :href="route('request-service')" variant="light">{{ __('header.request_service') }}</x-ui.button>
    </x-ui.cta-band>
@endsection
