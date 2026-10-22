@props(['name', 'type' => 'text'])

<input
    id="{{ $name }}"
    type="{{ $type }}"
    wire:model="{{ $name }}"
    @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
    {{ $attributes->merge(['class' => 'form-input']) }}
>
