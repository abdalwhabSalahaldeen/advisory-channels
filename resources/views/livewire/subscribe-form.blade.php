<div>
    @if ($submitted)
        <p class="flex items-center gap-3 rounded-2xl border border-brand-500/30 bg-brand-500/10 px-5 py-4 font-semibold text-brand-300" role="status">
            <x-ui.icon name="check" class="size-5" />
            {{ __('footer.subscribed') }}
        </p>
    @else
        <form wire:submit="submit" novalidate>
            <div class="flex flex-col gap-3 sm:flex-row">
                <label for="subscribe-email" class="sr-only">{{ __('footer.subscribe_placeholder') }}</label>
                <input
                    id="subscribe-email"
                    type="email"
                    wire:model="email"
                    dir="ltr"
                    autocomplete="email"
                    placeholder="{{ __('footer.subscribe_placeholder') }}"
                    @error('email') aria-invalid="true" aria-describedby="subscribe-email-error" @enderror
                    class="w-full rounded-full border border-white/15 bg-white/5 px-6 py-3.5 text-white placeholder:text-white/40 focus:border-brand-400 focus:ring-4 focus:ring-brand-400/15 focus:outline-none rtl:text-right"
                >
                <input type="text" wire:model="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
                <x-ui.button type="submit" wire:loading.attr="disabled" class="shrink-0">
                    {{ __('footer.subscribe_button') }}
                </x-ui.button>
            </div>
            @error('email') <p id="subscribe-email-error" class="form-error text-red-400!">{{ $message }}</p> @enderror
            @error('form') <p class="form-error text-red-400!">{{ $message }}</p> @enderror
            <p class="mt-3 text-xs text-white/40">
                {!! __('footer.subscribe_privacy', ['link' => '<a href="'.route('privacy-policy').'" class="underline hover:text-white">'.e(__('footer.privacy')).'</a>']) !!}
            </p>
        </form>
    @endif
</div>
