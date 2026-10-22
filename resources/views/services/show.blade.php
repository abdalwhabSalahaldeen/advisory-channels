@extends('layouts.app')

@section('title', $service->tr('meta_title') ?: $service->tr('title'))
@section('description', $service->tr('meta_description') ?: $service->tr('excerpt'))

@section('content')
    <x-ui.page-hero :eyebrow="__('services.eyebrow')" :title="$service->tr('title')" :text="$service->tr('excerpt')" />

    <section class="py-24 sm:py-32">
        <div class="container-site grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-8">
                @if ($service->image)
                    <img src="{{ Storage::disk('public')->url($service->image) }}" alt="" class="mb-12 aspect-[16/9] w-full rounded-3xl object-cover" data-reveal>
                @endif

                <div class="prose-site" data-reveal>{!! $service->tr('body') !!}</div>

                @if ($highlights = $service->trList('highlights'))
                    <h2 class="mt-16 text-2xl font-extrabold" data-reveal>{{ __('services.highlights') }}</h2>
                    <ul class="mt-8 grid gap-4 sm:grid-cols-2">
                        @foreach ($highlights as $highlight)
                            <li class="flex items-start gap-4 rounded-2xl border border-ink-900/5 bg-white p-6" data-reveal data-reveal-delay="{{ $loop->index * 0.05 }}">
                                <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-700 text-white">
                                    <x-ui.icon name="check" class="size-4" />
                                </span>
                                <span class="font-semibold leading-relaxed">{{ $highlight }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <aside class="lg:col-span-4">
                <div class="sticky top-28 space-y-5">
                    <div class="relative overflow-hidden rounded-3xl bg-ink-950 p-8 text-white">
                        <div class="grid-lines absolute inset-0" aria-hidden="true"></div>
                        <div class="relative">
                            <h2 class="text-2xl font-extrabold">{{ __('services.sidebar_title') }}</h2>
                            <p class="mt-3 leading-relaxed text-white/70">{{ __('services.sidebar_text') }}</p>
                            <x-ui.button :href="route('request-service', ['service' => $service->slug])" class="mt-8 w-full">{{ __('global.actions.request_this_service') }}</x-ui.button>
                        </div>
                    </div>

                    @if ($otherServices->isNotEmpty())
                        <nav class="rounded-3xl border border-ink-900/5 bg-white p-8" aria-label="{{ __('services.other') }}">
                            <p class="text-sm font-bold text-ink-700">{{ __('services.other') }}</p>
                            <ul class="mt-4 divide-y divide-ink-900/5">
                                @foreach ($otherServices as $other)
                                    <li>
                                        <a href="{{ route('services.show', $other) }}" class="group flex items-center justify-between gap-4 py-4 font-bold transition hover:text-brand-700">
                                            {{ $other->tr('title') }}
                                            <x-ui.icon name="arrow" class="size-4 text-ink-900/30 transition group-hover:text-brand-700 rtl:-scale-x-100" />
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </nav>
                    @endif
                </div>
            </aside>
        </div>
    </section>
@endsection
