@extends('layouts.app')

@section('title', $insight->tr('title'))
@section('description', $insight->tr('excerpt'))

@push('head')
    <meta property="article:published_time" content="{{ $insight->published_at->toAtomString() }}">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $insight->tr('title'),
            'description' => $insight->tr('excerpt'),
            'datePublished' => $insight->published_at->toAtomString(),
            'dateModified' => $insight->updated_at->toAtomString(),
            'inLanguage' => app()->getLocale(),
            'author' => $insight->author ? ['@type' => 'Person', 'name' => $insight->author->tr('name')] : ['@type' => 'Organization', 'name' => $site->tr('site_name')],
            'publisher' => ['@type' => 'Organization', 'name' => $site->tr('site_name', 'en')],
            'image' => $insight->cover ? Storage::disk('public')->url($insight->cover) : null,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <div class="reading-progress" data-reading-progress aria-hidden="true"></div>

    <x-ui.page-hero
        :eyebrow="$insight->type->getLabel()"
        :title="$insight->tr('title')"
        :text="$insight->tr('excerpt')"
        :image="$insight->cover ? Storage::disk('public')->url($insight->cover) : null"
    >
        <p class="mt-10 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-white/60" data-reveal data-reveal-delay="0.3">
            @if ($insight->author)
                <span>{{ __('insights.by') }} <span class="text-white">{{ $insight->author->tr('name') }}</span></span>
                <span class="size-1 rounded-full bg-gold-400" aria-hidden="true"></span>
            @endif
            <time datetime="{{ $insight->published_at->toDateString() }}">{{ $insight->published_at->locale(app()->getLocale())->translatedFormat('j F Y') }}</time>
            <span class="size-1 rounded-full bg-gold-400" aria-hidden="true"></span>
            <span>{{ __('insights.reading_time', ['minutes' => $insight->readingMinutes()]) }}</span>
        </p>
    </x-ui.page-hero>

    <section class="py-20 sm:py-28">
        <div class="container-site grid gap-16 lg:grid-cols-12">
            {{-- Share rail --}}
            <aside class="order-2 lg:order-1 lg:col-span-2">
                <div class="lg:sticky lg:top-28" x-data="{ copied: false }">
                    <p class="text-xs font-medium tracking-wide text-ink-700/60">{{ __('insights.share') }}</p>
                    @php($shareUrl = urlencode(url()->current()))
                    @php($shareText = urlencode($insight->tr('title')))
                    <div class="mt-4 flex gap-2 lg:flex-col">
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-sm border border-ink-900/15 text-ink-700 transition hover:border-brand-700 hover:text-brand-700" aria-label="LinkedIn"><x-ui.icon name="linkedin" class="size-4" /></a>
                        <a href="https://x.com/intent/post?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-sm border border-ink-900/15 text-ink-700 transition hover:border-brand-700 hover:text-brand-700" aria-label="X"><x-ui.icon name="x" class="size-4" /></a>
                        <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-sm border border-ink-900/15 text-ink-700 transition hover:border-brand-700 hover:text-brand-700" aria-label="WhatsApp"><x-ui.icon name="whatsapp" class="size-4" /></a>
                        <button type="button" @click="navigator.clipboard.writeText(@js(url()->current())); copied = true; setTimeout(() => copied = false, 2000)" class="inline-flex size-11 items-center justify-center rounded-sm border border-ink-900/15 text-ink-700 transition hover:border-brand-700 hover:text-brand-700" :aria-label="copied ? @js(__('insights.copied')) : @js(__('insights.copy_link'))">
                            <x-ui.icon name="file" class="size-4" x-show="! copied" />
                            <x-ui.icon name="check" class="size-4 text-brand-700" x-show="copied" x-cloak />
                        </button>
                    </div>
                </div>
            </aside>

            <article class="order-1 lg:order-2 lg:col-span-8" data-article>
                <div class="prose-site">{!! $insight->tr('body') !!}</div>

                @if ($insight->attachment)
                    <a href="{{ Storage::disk('public')->url($insight->attachment) }}" target="_blank" class="mt-12 flex items-center justify-between gap-6 border border-gold-500/40 bg-sand-50 p-6 transition hover:border-gold-500">
                        <span class="flex items-center gap-4">
                            <span class="inline-flex size-12 items-center justify-center bg-forest-900 text-gold-300"><x-ui.icon name="file" /></span>
                            <span class="text-lg">{{ __('insights.download_report') }}</span>
                        </span>
                        <x-ui.icon name="arrow-down" class="size-5 text-gold-600" />
                    </a>
                @endif

                @if ($insight->service)
                    <div class="mt-12 border-t border-ink-900/10 pt-8">
                        <p class="text-xs font-medium tracking-wide text-ink-700/60">{{ __('insights.related_service') }}</p>
                        <a href="{{ route('services.show', $insight->service) }}" class="mt-3 inline-flex items-center gap-3 text-xl text-brand-700 hover:text-brand-800">
                            {{ $insight->service->tr('title') }}
                            <x-ui.icon name="arrow" class="size-4 rtl:-scale-x-100" />
                        </a>
                    </div>
                @endif
            </article>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="bg-sand-50 py-20 sm:py-28">
            <div class="container-site">
                <x-ui.section-heading :title="__('insights.related')" />
                <div class="mt-14 grid gap-x-8 gap-y-14 md:grid-cols-3">
                    @foreach ($related as $item)
                        <x-ui.insight-card :insight="$item" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-ui.cta-band :title="__('insights.subscribe_title')" :text="__('insights.subscribe_text')">
        <div class="w-full min-w-80 lg:w-[28rem]"><livewire:subscribe-form /></div>
    </x-ui.cta-band>
@endsection
