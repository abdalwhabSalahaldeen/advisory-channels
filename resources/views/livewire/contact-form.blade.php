<div>
    @if ($submitted)
        <x-ui.form-success :message="__('forms.success_lead')" />
    @else
        <form wire:submit="submit" class="space-y-7" novalidate>
            <fieldset>
                <legend class="form-label">{{ __('forms.purpose') }}</legend>
                <div class="mt-1 grid gap-2 sm:grid-cols-2">
                    @foreach (App\Livewire\ContactForm::PURPOSES as $option)
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="purpose" value="{{ $option }}" class="peer sr-only">
                            <span class="flex h-full items-center gap-3 rounded-sm border border-ink-900/12 px-4 py-3.5 text-sm text-ink-700 transition peer-checked:border-brand-700 peer-checked:bg-brand-50 peer-checked:text-brand-800 peer-focus-visible:ring-4 peer-focus-visible:ring-brand-700/15 hover:border-gold-500">
                                <span class="size-2 rounded-full border border-current" aria-hidden="true"></span>
                                {{ __("forms.purposes.{$option}") }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('purpose')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </fieldset>

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
                <x-ui.field :label="__('forms.company')" for="company" :optional="$purpose !== 'rfp'">
                    <x-ui.input name="company" autocomplete="organization" />
                </x-ui.field>
            </div>
            <x-ui.field :label="__('forms.message')" for="message">
                <x-ui.textarea name="message" :placeholder="__('forms.message_hint')" />
            </x-ui.field>

            @if ($purpose === 'rfp')
                <x-ui.field :label="__('forms.attachment')" for="attachment" :hint="__('forms.attachment_hint')" optional>
                    <label for="attachment" class="flex cursor-pointer items-center gap-4 rounded-sm border border-dashed border-gold-500/60 bg-sand-50 p-5 transition hover:border-gold-500">
                        <span class="inline-flex size-11 shrink-0 items-center justify-center bg-forest-900 text-gold-300">
                            <x-ui.icon :name="$attachment ? 'file' : 'upload'" class="size-5" />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate" wire:loading.remove wire:target="attachment">{{ $attachment ? $attachment->getClientOriginalName() : __('forms.cv_choose') }}</span>
                            <span class="block text-brand-700" wire:loading wire:target="attachment">{{ __('forms.cv_uploading') }}</span>
                        </span>
                    </label>
                    <input id="attachment" type="file" wire:model="attachment" accept=".pdf,.doc,.docx,.zip" class="sr-only" @error('attachment') aria-invalid="true" aria-describedby="attachment-error" @enderror>
                </x-ui.field>
            @endif

            <x-ui.form-extras />

            <x-ui.button type="submit" class="w-full sm:w-auto" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">{{ __('global.actions.submit') }}</span>
                <span wire:loading wire:target="submit">{{ __('global.actions.sending') }}</span>
            </x-ui.button>
        </form>
    @endif
</div>
