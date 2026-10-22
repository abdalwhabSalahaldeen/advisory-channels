@props(['caseStudy'])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden bg-forest-950 text-white']) }}>
    <div class="relative aspect-[16/10] overflow-hidden">
        @if ($caseStudy->cover)
            <img src="{{ Storage::disk('public')->url($caseStudy->cover) }}" alt="" loading="lazy" class="size-full object-cover opacity-80 transition duration-[1.6s] ease-out-expo group-hover:scale-105">
        @else
            <div class="pattern-star absolute inset-0 text-gold-300/10" aria-hidden="true"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-forest-950 to-transparent" aria-hidden="true"></div>
        @if ($metric = collect($caseStudy->metrics)->first())
            <p class="absolute start-6 bottom-5">
                <span class="block font-display text-5xl font-light text-gold-300" dir="ltr">{{ $metric['value'] }}</span>
                <span class="mt-1 block text-sm text-white/70">{{ App\Support\Translatable::pick($metric['label'] ?? null) }}</span>
            </p>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-7">
        <p class="text-xs tracking-wide text-gold-400">{{ collect([$caseStudy->tr('sector'), $caseStudy->tr('client')])->filter()->implode(' · ') }}</p>
        <h3 class="heading-display mt-3 text-2xl leading-snug">
            <a href="{{ route('impact.show', $caseStudy) }}" class="after:absolute after:inset-0">{{ $caseStudy->tr('title') }}</a>
        </h3>
        <p class="mt-3 line-clamp-2 text-white/60">{{ $caseStudy->tr('summary') }}</p>
        <span class="mt-auto inline-flex items-center gap-2 pt-6 text-sm text-gold-300">
            {{ __('impact.read_story') }}
            <x-ui.icon name="arrow" class="size-4 transition group-hover:translate-x-1.5 rtl:-scale-x-100 rtl:group-hover:-translate-x-1.5" />
        </span>
    </div>
</article>
