@props(['eyebrow' => null, 'title', 'text' => null, 'image' => null])

{{-- Inner-page hero: deep royal green, star lattice, optional photograph (the fixed header sits on top). --}}
<section class="relative isolate overflow-hidden bg-forest-950 pt-44 pb-24 text-white sm:pt-52 sm:pb-32">
    @if ($image)
        <img src="{{ $image }}" alt="" class="ken-burns absolute inset-0 -z-20 size-full object-cover opacity-45">
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-forest-950 via-forest-950/70 to-forest-950/30" aria-hidden="true"></div>
    @else
        <canvas data-network class="absolute inset-0 -z-10 size-full opacity-50" aria-hidden="true"></canvas>
        <div class="absolute -top-56 start-1/4 -z-10 size-[44rem] rounded-full bg-brand-700/25 blur-[140px]" aria-hidden="true"></div>
    @endif
    <div class="pattern-star absolute inset-0 -z-10 text-white/[0.035]" aria-hidden="true"></div>

    <div class="container-site">
        <div class="max-w-4xl">
            @if ($eyebrow)
                <p class="eyebrow text-gold-400!" data-reveal>{{ $eyebrow }}</p>
            @endif
            <h1 class="heading-display mt-7 text-5xl leading-[1.08] sm:text-6xl lg:text-7xl" data-split>{{ $title }}</h1>
            @if ($text)
                <p class="mt-8 max-w-2xl text-lg leading-[1.85] text-white/70 text-pretty" data-reveal data-reveal-delay="0.2">{{ $text }}</p>
            @endif
            {{ $slot }}
        </div>
    </div>

    <div class="hairline absolute inset-x-0 bottom-0 opacity-60" aria-hidden="true"></div>
</section>
