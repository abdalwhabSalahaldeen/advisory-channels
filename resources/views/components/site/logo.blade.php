@props(['dark' => false])

@php($logo = $site->get('site_logo'))

<span {{ $attributes->merge(['class' => 'inline-flex items-center']) }}>
    @if ($logo)
        <img src="{{ Storage::disk('public')->url($logo) }}" alt="{{ $site->tr('site_name') }}" class="h-10 w-auto" />
    @else
        {{-- Official brand logo: the white version on dark backgrounds, the colour version on light ones. --}}
        <img src="{{ asset($dark ? 'images/brand/logo-white.svg' : 'images/brand/logo.svg') }}" alt="{{ $site->tr('site_name') }}" width="1534" height="277" class="h-9 w-auto sm:h-10" />
    @endif
</span>
