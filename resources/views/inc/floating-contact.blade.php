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
        <svg viewBox="0 0 24 24" class="relative size-7" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.3.1-.2 0-.3 0-.5l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z" /></svg>
    @else
        <x-ui.icon name="mail" class="relative size-6" />
    @endif
</a>
