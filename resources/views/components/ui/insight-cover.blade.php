@props(['insight'])

{{-- Cover photo, or a typographic cover in the brand colours when none was uploaded. --}}
<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-forest-900']) }}>
    @if ($insight->cover)
        <img src="{{ Storage::disk('public')->url($insight->cover) }}" alt="" loading="lazy" class="size-full object-cover transition duration-[1.6s] ease-out-expo group-hover:scale-105">
    @else
        <div class="pattern-star absolute inset-0 text-gold-300/10" aria-hidden="true"></div>
        <div class="absolute -end-16 -bottom-16 size-64 rounded-full bg-brand-600/30 blur-[70px]" aria-hidden="true"></div>
        <span class="absolute start-6 bottom-4 font-display text-[7rem] leading-none font-light text-white/10 transition duration-700 group-hover:text-gold-300/20" aria-hidden="true">&ldquo;</span>
    @endif
    <span class="absolute start-5 top-5 rounded-sm bg-forest-950/70 px-3 py-1.5 text-[0.7rem] font-medium tracking-wide text-gold-300 backdrop-blur">{{ $insight->type->getLabel() }}</span>
</div>
