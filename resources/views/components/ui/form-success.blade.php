@props(['message'])

<div class="py-10 text-center" role="status">
    <span class="mx-auto inline-flex size-20 items-center justify-center rounded-full bg-brand-50 text-brand-700 ring-8 ring-brand-50/50">
        <x-ui.icon name="check" class="size-10" />
    </span>
    <h3 class="mt-8 heading-display text-3xl">{{ __('forms.success_title') }}</h3>
    <p class="mx-auto mt-3 max-w-md leading-relaxed text-ink-700">{{ $message }}</p>
    {{ $slot }}
</div>
