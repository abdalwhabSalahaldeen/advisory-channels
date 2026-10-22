@props(['href' => null, 'variant' => 'primary', 'icon' => 'arrow'])

@php
    $classes = match ($variant) {
        'primary' => 'bg-brand-700 text-white hover:bg-brand-800',
        'gold' => 'bg-gold-500 text-forest-950 hover:bg-gold-400',
        'light' => 'bg-white text-forest-900 hover:bg-sand-100',
        'outline-light' => 'border border-white/30 text-white hover:border-gold-400 hover:text-gold-300',
        'outline' => 'border border-ink-900/20 text-ink-900 hover:border-brand-700 hover:text-brand-700',
        'ghost' => 'text-brand-700 hover:text-brand-800 px-0!',
    };
@endphp

<{{ $href ? 'a' : 'button' }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => "group inline-flex items-center justify-center gap-4 whitespace-nowrap rounded-sm px-7 py-4 text-sm font-medium tracking-wide transition duration-500 ease-out-expo disabled:cursor-not-allowed disabled:opacity-60 {$classes}"]) }}
>
    <span>{{ $slot }}</span>
    @if ($icon)
        <x-ui.icon :name="$icon" class="size-4 transition-transform duration-500 group-hover:translate-x-1.5 rtl:-scale-x-100 rtl:group-hover:-translate-x-1.5" />
    @endif
</{{ $href ? 'a' : 'button' }}>
