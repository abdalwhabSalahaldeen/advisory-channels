@props(['dark' => false])

@php($logo = $site->get('site_logo'))

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    @if ($logo)
        <img src="{{ Storage::disk('public')->url($logo) }}" alt="{{ $site->tr('site_name') }}" class="h-10 w-auto" />
    @else
        {{-- Placeholder mark (palm inside the bracket frame) until the official SVG logo is uploaded in the admin. --}}
        <svg viewBox="0 0 48 48" class="size-11 shrink-0" aria-hidden="true">
            <path d="M3 15V3h12M45 33v12H33" fill="none" stroke="currentColor" stroke-width="3" @class(['text-white' => $dark, 'text-ink-900' => ! $dark]) />
            <rect x="9" y="9" width="30" height="30" rx="2" class="fill-brand-700" />
            <path d="M24 35c.4-4.6.3-9 0-13" stroke="#fff" stroke-width="2" stroke-linecap="round" fill="none" />
            <path d="M24 22c-2.4-3.2-6-4.2-9.5-3.2 2.8.2 5.6 1.4 7.6 3.6M24 22c2.4-3.2 6-4.2 9.5-3.2-2.8.2-5.6 1.4-7.6 3.6M24 22c-1.2-3.6-3.6-6-7-6.8 2.4 1.2 4.4 3.4 5.4 6M24 22c1.2-3.6 3.6-6 7-6.8-2.4 1.2-4.4 3.4-5.4 6M24 22c0-3.6.8-6.4 2.4-8.6-1.6 1-2.6 2.8-3 4.8" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" />
        </svg>
        <span class="flex flex-col leading-none" dir="ltr">
            <span @class(['font-latin text-lg font-extrabold tracking-[0.12em]', 'text-white' => $dark, 'text-ink-900' => ! $dark])>ADVISORY</span>
            <span @class(['mt-1 font-latin text-[0.6rem] font-semibold tracking-[0.55em]', 'text-white/70' => $dark, 'text-ink-700' => ! $dark])>CHANNELS</span>
        </span>
    @endif
</span>
