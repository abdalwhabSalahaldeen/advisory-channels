{{-- Standalone page: it must render before the database exists, so it uses no site settings. --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ App\Support\Localization::direction() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>{{ __('install.title') }} | {{ config('app.name') }}</title>
        @fonts
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-dvh bg-forest-950 py-16 text-white">
        <main class="mx-auto w-full max-w-2xl px-4">
            <p class="font-latin text-lg font-semibold tracking-[0.25em]" dir="ltr">ADVISORY <span class="text-gold-300">CHANNELS</span></p>
            <h1 class="heading-display mt-10 text-4xl">{{ __('install.title') }}</h1>

            @if ($steps === null)
                <p class="mt-4 leading-relaxed text-white/70">{{ __('install.intro') }}</p>

                <h2 class="mt-12 text-sm font-medium text-gold-300">{{ __('install.requirements') }}</h2>
                <ul class="mt-4 divide-y divide-white/10 border-y border-white/10">
                    @foreach ($requirements as $check)
                        <li class="flex items-center justify-between gap-6 py-3 text-sm">
                            <span>{{ $check['label'] }}</span>
                            <span @class(['font-latin text-xs', 'text-brand-300' => $check['passes'], 'text-red-400' => ! $check['passes']]) dir="ltr">{{ $check['passes'] ? '✓' : '✗' }} {{ Str::limit($check['detail'], 60) }}</span>
                        </li>
                    @endforeach
                </ul>

                @if (! $ready)
                    <p class="mt-6 rounded-sm border border-red-400/40 bg-red-500/10 p-4 text-sm text-red-200">{{ __('install.not_ready') }}</p>
                @else
                    <form method="POST" action="{{ route('install.store') }}" class="mt-12 space-y-5 rounded-sm bg-white p-6 text-ink-900 sm:p-8">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div>
                            <h2 class="text-lg font-medium">{{ __('install.account') }}</h2>
                            <p class="mt-1 text-sm text-ink-700">{{ __('install.account_hint') }}</p>
                        </div>

                        @foreach ([['name', 'text', 'name'], ['email', 'email', 'email'], ['password', 'password', 'new-password'], ['password_confirmation', 'password', 'new-password']] as [$field, $type, $autocomplete])
                            <div>
                                <label for="{{ $field }}" class="form-label">{{ __("install.{$field}") }}</label>
                                <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" autocomplete="{{ $autocomplete }}" value="{{ $type === 'password' ? '' : old($field) }}" required @if ($type !== 'text') dir="ltr" @endif @error($field) aria-invalid="true" @enderror class="form-input">
                                @if ($field === 'password')
                                    <p class="mt-2 text-xs text-ink-700/70">{{ __('install.password_hint') }}</p>
                                @endif
                                @error($field)
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <x-ui.button type="submit" class="w-full">{{ __('install.submit') }}</x-ui.button>
                    </form>
                @endif
            @else
                @php($succeeded = collect($steps)->every(fn ($step) => $step['passes']))

                <h2 class="mt-12 text-sm font-medium text-gold-300">{{ __('install.result') }}</h2>
                <ul class="mt-4 divide-y divide-white/10 border-y border-white/10">
                    @foreach ($steps as $step)
                        <li class="py-3 text-sm">
                            <span @class(['text-brand-300' => $step['passes'], 'text-red-400' => ! $step['passes']])>{{ $step['passes'] ? '✓' : '✗' }}</span>
                            {{ $step['label'] }}
                            @unless ($step['passes'])
                                <pre class="mt-2 overflow-x-auto rounded-sm bg-black/40 p-3 text-xs whitespace-pre-wrap text-red-200" dir="ltr">{{ $step['detail'] }}</pre>
                            @endunless
                        </li>
                    @endforeach
                </ul>

                <p @class(['mt-8 text-lg', 'text-brand-300' => $succeeded, 'text-red-300' => ! $succeeded])>{{ $succeeded ? __('install.done') : __('install.failed') }}</p>

                @if ($succeeded)
                    <p class="mt-3 text-sm text-gold-300">{{ __('install.remove_token') }}</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <x-ui.button href="/management" variant="gold">{{ __('install.open_dashboard') }}</x-ui.button>
                        <x-ui.button href="/" variant="outline-light">{{ __('install.open_site') }}</x-ui.button>
                    </div>
                @endif
            @endif
        </main>
    </body>
</html>
