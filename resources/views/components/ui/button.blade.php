@props(['href' => null, 'variant' => 'primary', 'icon' => 'arrow'])

@php
    $classes = match ($variant) {
        'primary' => 'bg-brand-700 text-white hover:bg-brand-600 shadow-lg shadow-brand-900/20',
        'light' => 'bg-white text-ink-900 hover:bg-brand-50',
        'outline-light' => 'border border-white/25 text-white hover:border-white hover:bg-white/5',
        'outline' => 'border border-ink-900/15 text-ink-900 hover:border-brand-700 hover:text-brand-700',
        'ghost' => 'text-brand-700 hover:text-brand-600 px-0!',
    };
@endphp

<{{ $href ? 'a' : 'button' }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => "group inline-flex items-center justify-center gap-3 whitespace-nowrap rounded-full px-6 py-3.5 text-sm font-bold transition duration-300 ease-out-expo disabled:cursor-not-allowed disabled:opacity-60 {$classes}"]) }}
>
    <span>{{ $slot }}</span>
    @if ($icon)
        <x-ui.icon :name="$icon" class="size-4 transition-transform duration-300 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1" />
    @endif
</{{ $href ? 'a' : 'button' }}>
