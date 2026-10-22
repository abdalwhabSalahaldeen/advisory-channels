<div>
    @if ($submitted)
        <x-ui.form-success :message="__('forms.success_lead')">
            <button type="button" wire:click="startOver" class="mt-8 text-sm font-medium text-brand-700 underline underline-offset-4">{{ __('forms.send_another') }}</button>
        </x-ui.form-success>
    @else
        {{-- Progress --}}
        <div class="mb-10">
            <p class="text-sm font-semibold text-ink-700">{{ __('forms.step', ['current' => $step, 'total' => self::TOTAL_STEPS]) }}</p>
            <ol class="mt-4 grid grid-cols-3 gap-2">
                @foreach (__('forms.steps') as $label)
                    <li>
                        <span @class(['block h-1.5 rounded-full transition-colors duration-500', 'bg-brand-700' => $loop->iteration <= $step, 'bg-ink-900/10' => $loop->iteration > $step])></span>
                        <span @class(['mt-2 block text-xs font-medium', 'text-brand-700' => $loop->iteration === $step, 'text-ink-700/60' => $loop->iteration !== $step])>{{ $label }}</span>
                    </li>
                @endforeach
            </ol>
        </div>

        <form wire:submit="{{ $step < self::TOTAL_STEPS ? 'next' : 'submit' }}" novalidate>
            @if ($step === 1)
                <fieldset>
                    <legend class="text-xl font-medium">{{ __('forms.choose_service') }}</legend>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        @foreach ($this->services as $option)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="service" value="{{ $option->slug }}" class="peer sr-only">
                                <span class="flex h-full items-start gap-4 rounded-sm border border-ink-900/10 p-5 transition peer-checked:border-brand-700 peer-checked:bg-brand-50 peer-checked:ring-2 peer-checked:ring-brand-700 peer-focus-visible:ring-4 peer-focus-visible:ring-brand-500/30 hover:border-brand-500">
                                    <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-sm bg-brand-700 text-white">
                                        <x-ui.icon :name="$option->icon ?? 'compass'" class="size-5" />
                                    </span>
                                    <span>
                                        <span class="block font-medium">{{ $option->tr('title') }}</span>
                                        <span class="mt-1 block text-sm leading-relaxed text-ink-700">{{ $option->tr('excerpt') }}</span>
                                    </span>
                                </span>
                            </label>
                        @endforeach
                        <label class="cursor-pointer sm:col-span-2">
                            <input type="radio" wire:model="service" value="{{ self::NOT_SURE }}" class="peer sr-only">
                            <span class="flex items-center gap-4 rounded-sm border border-dashed border-ink-900/20 p-5 transition peer-checked:border-solid peer-checked:border-brand-700 peer-checked:bg-brand-50 peer-checked:ring-2 peer-checked:ring-brand-700 hover:border-brand-500">
                                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-sm bg-paper text-brand-700 font-medium">?</span>
                                <span>
                                    <span class="block font-medium">{{ __('forms.not_sure') }}</span>
                                    <span class="mt-1 block text-sm text-ink-700">{{ __('forms.not_sure_text') }}</span>
                                </span>
                            </span>
                        </label>
                    </div>
                    @error('service')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </fieldset>
            @elseif ($step === 2)
                <div class="space-y-6">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-ui.field :label="__('forms.company')" for="company">
                            <x-ui.input name="company" autocomplete="organization" />
                        </x-ui.field>
                        <x-ui.field :label="__('forms.job_title')" for="job_title" optional>
                            <x-ui.input name="job_title" autocomplete="organization-title" />
                        </x-ui.field>
                    </div>
                    <x-ui.field :label="__('forms.timeline')" for="timeline" optional>
                        <select id="timeline" wire:model="timeline" class="form-input">
                            <option value="">—</option>
                            @foreach (self::TIMELINES as $timeline)
                                <option value="{{ $timeline }}">{{ __("forms.timelines.{$timeline}") }}</option>
                            @endforeach
                        </select>
                    </x-ui.field>
                    <x-ui.field :label="__('forms.message')" for="message">
                        <x-ui.textarea name="message" :placeholder="__('forms.message_hint')" />
                    </x-ui.field>
                </div>
            @else
                <div class="space-y-6">
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
                    <x-ui.form-extras />
                </div>
            @endif

            <div class="mt-10 flex items-center justify-between gap-4 border-t border-ink-900/5 pt-8">
                @if ($step > 1)
                    <button type="button" wire:click="back" class="inline-flex items-center gap-2 text-sm font-medium text-ink-700 transition hover:text-brand-700">
                        <x-ui.icon name="arrow" class="size-4 -scale-x-100 rtl:scale-x-100" />
                        {{ __('global.actions.back') }}
                    </button>
                @else
                    <span></span>
                @endif

                <x-ui.button type="submit" wire:loading.attr="disabled">
                    @if ($step < self::TOTAL_STEPS)
                        {{ __('global.actions.next') }}
                    @else
                        <span wire:loading.remove wire:target="submit">{{ __('global.actions.submit') }}</span>
                        <span wire:loading wire:target="submit">{{ __('global.actions.sending') }}</span>
                    @endif
                </x-ui.button>
            </div>
        </form>
    @endif
</div>
