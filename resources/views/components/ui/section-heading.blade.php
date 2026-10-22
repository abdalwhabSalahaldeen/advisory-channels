@props(['eyebrow' => null, 'title', 'text' => null, 'align' => 'start', 'dark' => false])

<div {{ $attributes->class(['max-w-3xl', 'mx-auto text-center' => $align === 'center']) }} data-reveal>
    @if ($eyebrow)
        <p @class(['eyebrow', 'text-brand-400!' => $dark])>{{ $eyebrow }}</p>
    @endif
    <h2 @class(['mt-4 text-3xl font-extrabold leading-tight tracking-tight text-balance sm:text-4xl lg:text-5xl', 'text-white' => $dark, 'text-ink-900' => ! $dark])>
        {{ $title }}
    </h2>
    @if ($text)
        <p @class(['mt-5 text-lg leading-relaxed text-pretty', 'text-white/70' => $dark, 'text-ink-700' => ! $dark])>{{ $text }}</p>
    @endif
</div>
