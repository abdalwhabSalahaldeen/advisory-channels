@props(['name', 'rows' => 5])

<textarea
    id="{{ $name }}"
    rows="{{ $rows }}"
    wire:model="{{ $name }}"
    @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
    {{ $attributes->merge(['class' => 'form-input resize-y']) }}
></textarea>
