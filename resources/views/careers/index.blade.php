@extends('layouts.app')

@section('title', __('careers.meta_title'))
@section('description', __('careers.text'))

@section('content')
    <x-ui.page-hero :eyebrow="__('careers.eyebrow')" :title="__('careers.title')" :text="__('careers.text')" />

    {{-- Why join --}}
    <section class="py-24 sm:py-32">
        <div class="container-site">
            <x-ui.section-heading :title="__('careers.why_title')" />
            <div class="mt-14 grid gap-5 md:grid-cols-3">
                @foreach (__('careers.why') as $reason)
                    <div class="rounded-sm border border-ink-900/5 bg-white p-8" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                        <span class="inline-flex size-12 items-center justify-center rounded-sm bg-brand-50 text-brand-700">
                            <x-ui.icon :name="['briefcase', 'star', 'chart'][$loop->index] ?? 'star'" />
                        </span>
                        <h3 class="mt-6 text-xl font-medium">{{ $reason['title'] }}</h3>
                        <p class="mt-3 leading-relaxed text-ink-700">{{ $reason['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Openings --}}
    <section class="bg-white py-24 sm:py-32">
        <div class="container-site">
            <x-ui.section-heading :title="__('careers.openings_title')" />

            <div class="mt-12 space-y-4">
                @forelse ($openings as $opening)
                    <a href="{{ route('careers.show', $opening) }}" class="card-lift group flex flex-col gap-6 rounded-sm border border-ink-900/5 bg-paper p-8 sm:flex-row sm:items-center sm:justify-between" data-reveal>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 text-xs font-medium">
                                <span class="rounded-full bg-brand-700 px-3 py-1 text-white">{{ $opening->type->getLabel() }}</span>
                                @if ($location = $opening->tr('location'))
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 text-ink-700">
                                        <x-ui.icon name="pin" class="size-3.5" /> {{ $location }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="mt-4 heading-display text-3xl">{{ $opening->tr('title') }}</h3>
                            <p class="mt-2 max-w-2xl leading-relaxed text-ink-700">{{ $opening->tr('summary') }}</p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-2 text-sm font-medium text-brand-700">
                            {{ __('global.actions.view_opening') }}
                            <x-ui.icon name="arrow" class="size-4 transition group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1" />
                        </span>
                    </a>
                @empty
                    <p class="rounded-sm border border-dashed border-ink-900/15 p-10 text-center text-lg text-ink-700">{{ __('careers.no_openings') }}</p>
                @endforelse
            </div>

            <div class="mt-12 flex flex-col items-start justify-between gap-6 rounded-sm bg-forest-950 p-8 text-white sm:flex-row sm:items-center sm:p-10" data-reveal>
                <div>
                    <h3 class="heading-display text-3xl">{{ __('careers.general_title') }}</h3>
                    <p class="mt-2 text-white/70">{{ __('careers.general_text') }}</p>
                </div>
                <x-ui.button :href="route('careers.apply')" variant="light" class="shrink-0">{{ __('global.actions.send_cv') }}</x-ui.button>
            </div>
        </div>
    </section>
@endsection
