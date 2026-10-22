@props(['eyebrow' => null, 'title', 'text' => null, 'align' => 'start', 'dark' => false, 'number' => null])

<div {{ $attributes->class(['max-w-3xl', 'mx-auto text-center' => $align === 'center']) }}>
    @if ($eyebrow || $number)
        <p @class(['eyebrow', 'text-gold-400!' => $dark]) data-reveal>
            @if ($number)
                <span class="font-latin">{{ $number }}</span>
            @endif
            {{ $eyebrow }}
        </p>
    @endif
    <h2 @class(['heading-display mt-6 text-4xl leading-[1.12] sm:text-5xl lg:text-[3.5rem]', 'text-white' => $dark, 'text-ink-900' => ! $dark]) data-split>
        {{ $title }}
    </h2>
    @if ($text)
        <p @class(['mt-7 text-lg leading-[1.85] text-pretty', 'text-white/65' => $dark, 'text-ink-700' => ! $dark]) data-reveal data-reveal-delay="0.15">{{ $text }}</p>
    @endif
</div>
