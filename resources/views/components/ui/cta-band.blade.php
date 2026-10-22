@props(['title', 'text' => null])

<section class="relative isolate overflow-hidden bg-forest-900 py-24 text-white sm:py-32">
    <div class="pattern-star absolute inset-0 -z-10 text-gold-300/[0.06]" aria-hidden="true"></div>
    <div class="absolute -end-40 -bottom-40 -z-10 size-[30rem] rounded-full bg-brand-600/25 blur-[120px]" aria-hidden="true"></div>

    <div class="container-site flex flex-col items-start justify-between gap-12 lg:flex-row lg:items-end">
        <div class="max-w-3xl">
            <h2 class="heading-display text-4xl leading-[1.12] sm:text-5xl" data-split>{{ $title }}</h2>
            @if ($text)
                <p class="mt-6 text-lg leading-[1.85] text-white/70" data-reveal>{{ $text }}</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-3" data-reveal data-reveal-delay="0.15">
            {{ $slot }}
        </div>
    </div>
</section>
