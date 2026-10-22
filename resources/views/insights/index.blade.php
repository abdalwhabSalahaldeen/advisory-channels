@extends('layouts.app')

@section('title', __('insights.meta_title'))
@section('description', __('insights.meta_description'))

@section('content')
    <x-ui.page-hero :eyebrow="__('insights.eyebrow')" :title="__('insights.title')" :text="__('insights.text')" />

    {{-- Featured story, McKinsey/BCG style --}}
    @if ($featured)
        <section class="py-20 sm:py-28">
            <div class="container-site">
                <article class="group relative grid items-center gap-10 lg:grid-cols-12 lg:gap-16">
                    <x-ui.insight-cover :insight="$featured" class="aspect-[16/10] lg:col-span-7" data-reveal-image />
                    <div class="lg:col-span-5">
                        <p class="eyebrow" data-reveal>{{ __('insights.featured') }}</p>
                        <h2 class="heading-display mt-6 text-4xl leading-[1.15] sm:text-5xl" data-split>
                            <a href="{{ route('insights.show', $featured) }}" class="after:absolute after:inset-0">{{ $featured->tr('title') }}</a>
                        </h2>
                        <p class="mt-6 text-lg leading-[1.85] text-ink-700" data-reveal>{{ $featured->tr('excerpt') }}</p>
                        <p class="mt-8 inline-flex items-center gap-3 text-sm font-medium text-brand-700" data-reveal>
                            {{ __('insights.read') }}
                            <span class="text-ink-700/60">· {{ __('insights.reading_time', ['minutes' => $featured->readingMinutes()]) }}</span>
                            <x-ui.icon name="arrow" class="size-4 transition group-hover:translate-x-1.5 rtl:-scale-x-100 rtl:group-hover:-translate-x-1.5" />
                        </p>
                    </div>
                </article>
            </div>
        </section>
        <div class="container-site"><div class="hairline"></div></div>
    @endif

    <section class="py-20 sm:py-28">
        <div class="container-site">
            {{-- Type filter --}}
            @if ($types->count() > 1)
                <nav class="flex flex-wrap gap-2" aria-label="{{ __('insights.eyebrow') }}">
                    <a href="{{ route('insights.index') }}" @class(['rounded-sm border px-5 py-2.5 text-sm transition', 'border-forest-900 bg-forest-900 text-white' => ! $type, 'border-ink-900/15 text-ink-700 hover:border-brand-700 hover:text-brand-700' => $type]) @if (! $type) aria-current="page" @endif>{{ __('insights.all') }}</a>
                    @foreach ($types as $option)
                        <a href="{{ route('insights.index', ['type' => $option->value]) }}" @class(['rounded-sm border px-5 py-2.5 text-sm transition', 'border-forest-900 bg-forest-900 text-white' => $type === $option, 'border-ink-900/15 text-ink-700 hover:border-brand-700 hover:text-brand-700' => $type !== $option]) @if ($type === $option) aria-current="page" @endif>{{ $option->getLabel() }}</a>
                    @endforeach
                </nav>
            @endif

            <div class="mt-14 grid gap-x-8 gap-y-16 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($insights as $insight)
                    <x-ui.insight-card :insight="$insight" data-reveal data-reveal-delay="{{ ($loop->index % 3) * 0.1 }}" />
                @empty
                    <p class="col-span-full border border-dashed border-ink-900/15 p-12 text-center text-lg text-ink-700">{{ __('insights.empty') }}</p>
                @endforelse
            </div>

            @if ($insights->hasPages())
                <div class="mt-16">{{ $insights->links() }}</div>
            @endif
        </div>
    </section>

    <x-ui.cta-band :title="__('insights.subscribe_title')" :text="__('insights.subscribe_text')">
        <div class="w-full min-w-80 lg:w-[28rem]"><livewire:subscribe-form /></div>
    </x-ui.cta-band>
@endsection
