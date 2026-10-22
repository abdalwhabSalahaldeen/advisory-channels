<div>
    @if ($submitted)
        <x-ui.form-success :message="__('forms.success_lead')" />
    @else
        <form wire:submit="submit" class="space-y-6" novalidate>
            <div class="grid gap-6 sm:grid-cols-2">
                <x-ui.field :label="__('forms.full_name')" for="full_name">
                    <x-ui.input name="full_name" autocomplete="name" />
                </x-ui.field>
                <x-ui.field :label="__('forms.email')" for="email">
                    <x-ui.input name="email" type="email" autocomplete="email" dir="ltr" />
                </x-ui.field>
                <x-ui.field :label="__('forms.phone')" for="phone" optional>
                    <x-ui.input name="phone" type="tel" autocomplete="tel" dir="ltr" />
                </x-ui.field>
                <x-ui.field :label="__('forms.company')" for="company" optional>
                    <x-ui.input name="company" autocomplete="organization" />
                </x-ui.field>
            </div>
            <x-ui.field :label="__('forms.message')" for="message">
                <x-ui.textarea name="message" :placeholder="__('forms.message_hint')" />
            </x-ui.field>

            <x-ui.form-extras />

            <x-ui.button type="submit" class="w-full sm:w-auto" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">{{ __('global.actions.submit') }}</span>
                <span wire:loading wire:target="submit">{{ __('global.actions.sending') }}</span>
            </x-ui.button>
        </form>
    @endif
</div>
