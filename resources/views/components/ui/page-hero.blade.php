@props(['eyebrow' => null, 'title', 'text' => null])

{{-- Dark hero used at the top of every inner page (the fixed header sits on top of it). --}}
<section class="relative isolate overflow-hidden bg-ink-950 pt-40 pb-20 text-white sm:pt-48 sm:pb-28">
    <canvas data-network class="absolute inset-0 -z-10 size-full opacity-70" aria-hidden="true"></canvas>
    <div class="grid-lines absolute inset-0 -z-10" aria-hidden="true"></div>
    <div class="absolute -top-40 start-1/2 -z-10 size-[42rem] -translate-x-1/2 rounded-full bg-brand-700/30 blur-[120px] rtl:translate-x-1/2" aria-hidden="true"></div>

    <div class="container-site">
        <div class="max-w-3xl" data-reveal>
            @if ($eyebrow)
                <p class="eyebrow text-brand-400!">{{ $eyebrow }}</p>
            @endif
            <h1 class="mt-5 text-4xl font-extrabold leading-[1.15] tracking-tight text-balance sm:text-5xl lg:text-6xl">{{ $title }}</h1>
            @if ($text)
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-white/70 text-pretty">{{ $text }}</p>
            @endif
            {{ $slot }}
        </div>
    </div>
</section>
