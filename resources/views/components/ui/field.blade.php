@props(['label', 'for', 'hint' => null, 'optional' => false])

<div {{ $attributes }}>
    <label for="{{ $for }}" class="form-label">
        {{ $label }}
        @if ($optional)
            <span class="font-normal text-ink-700/60">({{ __('forms.optional') }})</span>
        @endif
    </label>
    {{ $slot }}
    @if ($hint)
        <p class="mt-2 text-sm text-ink-700/70">{{ $hint }}</p>
    @endif
    @error($for)
        <p id="{{ $for }}-error" class="form-error">{{ $message }}</p>
    @enderror
</div>
