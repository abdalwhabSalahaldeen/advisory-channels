{{-- Consent checkbox, honeypot and form-level errors shared by every public form. --}}
<div>
    <label class="flex cursor-pointer items-start gap-3 text-sm leading-relaxed text-ink-700">
        <input
            id="consent"
            type="checkbox"
            wire:model="consent"
            class="mt-0.5 size-5 shrink-0 rounded border-ink-900/20 accent-brand-700"
            @error('consent') aria-invalid="true" aria-describedby="consent-error" @enderror
        >
        <span>{!! __('forms.consent', ['link' => '<a href="'.route('privacy-policy').'" target="_blank" class="font-semibold text-brand-700 underline underline-offset-4">'.e(__('forms.consent_link')).'</a>']) !!}</span>
    </label>
    @error('consent')
        <p id="consent-error" class="form-error">{{ $message }}</p>
    @enderror

    {{-- Honeypot: hidden from people, only bots fill it in. --}}
    <div class="hidden" aria-hidden="true">
        <input type="text" wire:model="website" tabindex="-1" autocomplete="off">
    </div>

    @error('form')
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
