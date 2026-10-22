{{-- Floating support button (the chat bubble from the Drovox site): WhatsApp when configured, otherwise the contact page. --}}
@php($whatsapp = preg_replace('/\D+/', '', (string) $site->get('whatsapp')))

<a
    href="{{ $whatsapp ? "https://wa.me/{$whatsapp}" : route('contact') }}"
    @if ($whatsapp) target="_blank" rel="noopener" @endif
    class="group fixed bottom-5 end-5 z-40 inline-flex size-14 items-center justify-center rounded-full bg-brand-700 text-white shadow-xl shadow-brand-950/30 ring-4 ring-brand-500/20 transition hover:scale-105 hover:bg-brand-600"
    aria-label="{{ $whatsapp ? __('global.floating_whatsapp') : __('global.floating_contact') }}"
>
    <span class="absolute inset-0 animate-ping rounded-full bg-brand-500/30 motion-reduce:hidden" aria-hidden="true"></span>
    @if ($whatsapp)
        <x-ui.icon name="whatsapp" class="relative size-7" />
    @else
        <x-ui.icon name="mail" class="relative size-6" />
    @endif
</a>
