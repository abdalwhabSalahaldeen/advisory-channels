<div>
    @if ($submitted)
        <x-ui.form-success :message="__('forms.success_application')" />
    @else
        <form wire:submit="submit" class="space-y-6" novalidate>
            <x-ui.field :label="__('forms.full_name')" for="full_name">
                <x-ui.input name="full_name" autocomplete="name" />
            </x-ui.field>
            <div class="grid gap-6 sm:grid-cols-2">
                <x-ui.field :label="__('forms.email')" for="email">
                    <x-ui.input name="email" type="email" autocomplete="email" dir="ltr" />
                </x-ui.field>
                <x-ui.field :label="__('forms.phone')" for="phone">
                    <x-ui.input name="phone" type="tel" autocomplete="tel" dir="ltr" />
                </x-ui.field>
            </div>
            <x-ui.field :label="__('forms.linkedin')" for="linkedin_url" optional>
                <x-ui.input name="linkedin_url" type="url" dir="ltr" placeholder="https://www.linkedin.com/in/..." />
            </x-ui.field>

            <x-ui.field :label="__('forms.cv')" for="cv" :hint="__('forms.cv_hint')">
                <label
                    for="cv"
                    class="flex cursor-pointer items-center gap-4 rounded-2xl border-2 border-dashed border-ink-900/15 p-5 transition hover:border-brand-500 hover:bg-brand-50/50"
                >
                    <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-brand-700 text-white">
                        <x-ui.icon :name="$cv ? 'file' : 'upload'" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate font-bold" wire:loading.remove wire:target="cv">
                            {{ $cv ? $cv->getClientOriginalName() : __('forms.cv_choose') }}
                        </span>
                        <span class="block font-bold text-brand-700" wire:loading wire:target="cv">{{ __('forms.cv_uploading') }}</span>
                    </span>
                </label>
                <input
                    id="cv"
                    type="file"
                    wire:model="cv"
                    accept=".pdf,.doc,.docx"
                    class="sr-only"
                    @error('cv') aria-invalid="true" aria-describedby="cv-error" @enderror
                >
            </x-ui.field>

            <x-ui.field :label="__('forms.cover_letter')" for="cover_letter" optional>
                <x-ui.textarea name="cover_letter" rows="4" />
            </x-ui.field>

            <x-ui.form-extras />

            <x-ui.button type="submit" class="w-full sm:w-auto" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">{{ __('global.actions.apply_now') }}</span>
                <span wire:loading wire:target="submit">{{ __('global.actions.sending') }}</span>
            </x-ui.button>
        </form>
    @endif
</div>
