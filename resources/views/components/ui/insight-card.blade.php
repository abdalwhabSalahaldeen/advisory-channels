@props(['insight'])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col']) }}>
    <x-ui.insight-cover :insight="$insight" class="aspect-[4/3]" />
    <div class="flex flex-1 flex-col pt-6">
        <p class="flex items-center gap-3 text-xs text-ink-700/70">
            <time datetime="{{ $insight->published_at->toDateString() }}">{{ $insight->published_at->locale(app()->getLocale())->translatedFormat('j F Y') }}</time>
            <span class="size-1 rounded-full bg-gold-500" aria-hidden="true"></span>
            <span>{{ __('insights.reading_time', ['minutes' => $insight->readingMinutes()]) }}</span>
        </p>
        <h3 class="heading-display mt-3 text-2xl leading-snug text-ink-900 transition group-hover:text-brand-700">
            <a href="{{ route('insights.show', $insight) }}" class="after:absolute after:inset-0">{{ $insight->tr('title') }}</a>
        </h3>
        <p class="mt-3 line-clamp-2 leading-relaxed text-ink-700">{{ $insight->tr('excerpt') }}</p>
    </div>
</article>
