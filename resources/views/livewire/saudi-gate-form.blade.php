<div>
    @if ($submitted)
        <x-ui.form-success :message="__('forms.success_lead')" />
    @else
        <form wire:submit="submit" class="space-y-6" novalidate>
            <fieldset>
                <legend class="form-label">{{ __('forms.choose_packages') }}</legend>
                <div class="mt-1 flex flex-wrap gap-2">
                    @foreach ($this->gatePackages as $package)
                        <label class="cursor-pointer">
                            <input type="checkbox" wire:model="packages" value="{{ $package->slug }}" class="peer sr-only">
                            <span class="inline-flex items-center gap-2 rounded-full border border-ink-900/10 px-4 py-2.5 text-sm font-medium text-ink-700 transition peer-checked:border-brand-700 peer-checked:bg-brand-700 peer-checked:text-white peer-focus-visible:ring-4 peer-focus-visible:ring-brand-500/30 hover:border-brand-500">
                                <span class="font-latin text-xs opacity-60">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                {{ $package->tr('title') }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('packages')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </fieldset>

            <div class="grid gap-6 sm:grid-cols-2">
                <x-ui.field :label="__('forms.full_name')" for="full_name">
                    <x-ui.input name="full_name" autocomplete="name" />
                </x-ui.field>
                <x-ui.field :label="__('forms.company')" for="company">
                    <x-ui.input name="company" autocomplete="organization" />
                </x-ui.field>
                <x-ui.field :label="__('forms.email')" for="email">
                    <x-ui.input name="email" type="email" autocomplete="email" dir="ltr" />
                </x-ui.field>
                <x-ui.field :label="__('forms.phone')" for="phone">
                    <x-ui.input name="phone" type="tel" autocomplete="tel" dir="ltr" />
                </x-ui.field>
                <x-ui.field :label="__('forms.country')" for="country" optional class="sm:col-span-2">
                    <x-ui.input name="country" autocomplete="country-name" />
                </x-ui.field>
            </div>
            <x-ui.field :label="__('forms.message')" for="message" optional>
                <x-ui.textarea name="message" rows="4" :placeholder="__('forms.message_hint')" />
            </x-ui.field>

            <x-ui.form-extras />

            <x-ui.button type="submit" class="w-full sm:w-auto" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">{{ __('global.actions.submit') }}</span>
                <span wire:loading wire:target="submit">{{ __('global.actions.sending') }}</span>
            </x-ui.button>
        </form>
    @endif
</div>
