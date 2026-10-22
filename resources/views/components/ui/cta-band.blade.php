@props(['title', 'text' => null])

<section class="relative isolate overflow-hidden bg-brand-800 py-20 text-white sm:py-24">
    <div class="grid-lines absolute inset-0 -z-10" aria-hidden="true"></div>
    <div class="absolute -end-32 -bottom-32 -z-10 size-96 rounded-full bg-brand-400/30 blur-[100px]" aria-hidden="true"></div>

    <div class="container-site flex flex-col items-start justify-between gap-10 lg:flex-row lg:items-center">
        <div class="max-w-2xl" data-reveal>
            <h2 class="text-3xl font-extrabold leading-tight text-balance sm:text-4xl">{{ $title }}</h2>
            @if ($text)
                <p class="mt-4 text-lg text-white/75">{{ $text }}</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-3" data-reveal data-reveal-delay="0.1">
            {{ $slot }}
        </div>
    </div>
</section>
