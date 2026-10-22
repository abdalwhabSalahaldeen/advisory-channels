@extends('layouts.app')

@section('title', $opening->tr('title'))
@section('description', $opening->tr('summary'))

@section('content')
    <x-ui.page-hero :eyebrow="$opening->type->getLabel()" :title="$opening->tr('title')" :text="$opening->tr('summary')">
        <div class="mt-8 flex flex-wrap gap-3 text-sm font-semibold text-white/70">
            @if ($location = $opening->tr('location'))
                <span class="inline-flex items-center gap-2"><x-ui.icon name="pin" class="size-4 text-gold-400" /> {{ $location }}</span>
            @endif
            @if ($opening->closes_at)
                <span class="inline-flex items-center gap-2">· {{ __('careers.closes_at', ['date' => $opening->closes_at->locale(app()->getLocale())->translatedFormat('j F Y')]) }}</span>
            @endif
        </div>
    </x-ui.page-hero>

    <section class="py-24 sm:py-32">
        <div class="container-site grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <h2 class="heading-display text-3xl" data-reveal>{{ __('careers.about_role') }}</h2>
                <div class="prose-site mt-6" data-reveal>{!! $opening->tr('description') !!}</div>

                @if ($requirements = $opening->trList('requirements'))
                    <h2 class="mt-14 heading-display text-3xl" data-reveal>{{ __('careers.requirements') }}</h2>
                    <ul class="mt-6 space-y-3">
                        @foreach ($requirements as $requirement)
                            <li class="flex items-start gap-3 text-lg text-ink-700" data-reveal>
                                <x-ui.icon name="check" class="mt-1 size-5 text-brand-600" />
                                {{ $requirement }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="lg:col-span-5">
                <div id="apply" class="sticky top-28 scroll-mt-28 rounded-sm border border-ink-900/5 bg-white p-6 sm:p-8">
                    @if ($opening->isOpen())
                        <h2 class="mb-8 heading-display text-3xl">{{ __('careers.apply_title') }}</h2>
                        <livewire:job-application-form :job-opening="$opening" />
                    @else
                        <p class="text-lg font-semibold text-ink-700">{{ __('careers.closed') }}</p>
                        <x-ui.button :href="route('careers.index')" variant="outline" class="mt-6">{{ __('global.actions.all_openings') }}</x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
