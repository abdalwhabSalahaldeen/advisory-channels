<footer class="relative overflow-hidden bg-forest-950 text-white">
    <div class="pattern-star absolute inset-0 text-white/[0.03]" aria-hidden="true"></div>

    {{-- Newsletter band, as on the Drovox site. --}}
    <div class="relative border-b border-white/10">
        <div class="container-site grid items-center gap-8 py-12 lg:grid-cols-2">
            <div>
                <p class="heading-display text-3xl">{{ __('footer.subscribe_title') }}</p>
                <p class="mt-2 text-white/60">{{ __('footer.subscribe_text') }}</p>
            </div>
            <livewire:subscribe-form />
        </div>
    </div>

    <div class="container-site relative grid gap-12 py-16 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <x-site.logo dark />
            <p class="mt-6 max-w-md leading-relaxed text-white/60">{{ __('footer.about') }}</p>

            <div class="mt-8 flex gap-3">
                @if ($linkedin = $site->get('linkedin_url'))
                    <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-full border border-white/15 text-white/70 transition hover:border-brand-400 hover:text-gold-400" aria-label="LinkedIn">
                        <x-ui.icon name="linkedin" class="size-5" />
                    </a>
                @endif
                @if ($x = $site->get('x_url'))
                    <a href="{{ $x }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-full border border-white/15 text-white/70 transition hover:border-brand-400 hover:text-gold-400" aria-label="X">
                        <x-ui.icon name="x" class="size-4" />
                    </a>
                @endif
            </div>
        </div>

        <div class="lg:col-span-3">
            <p class="text-sm font-medium text-white">{{ __('footer.explore') }}</p>
            <ul class="mt-5 space-y-3 text-white/60">
                <li><a href="{{ route('about') }}" class="transition hover:text-white">{{ __('header.about') }}</a></li>
                <li><a href="{{ route('services.index') }}" class="transition hover:text-white">{{ __('header.services') }}</a></li>
                <li><a href="{{ route('saudi-gate') }}" class="transition hover:text-white">{{ __('header.saudi_gate') }}</a></li>
                <li><a href="{{ route('careers.index') }}" class="transition hover:text-white">{{ __('header.careers') }}</a></li>
                <li><a href="{{ route('faqs') }}" class="transition hover:text-white">{{ __('footer.faqs') }}</a></li>
                <li><a href="{{ route('request-service') }}" class="transition hover:text-white">{{ __('header.request_service') }}</a></li>
            </ul>
        </div>

        <div class="lg:col-span-4">
            <p class="text-sm font-medium text-white">{{ __('footer.contact') }}</p>
            <ul class="mt-5 space-y-4 text-white/60">
                @if ($email = $site->get('contact_email'))
                    <li class="flex items-center gap-3">
                        <x-ui.icon name="mail" class="size-5 text-gold-400" />
                        <a href="mailto:{{ $email }}" class="transition hover:text-white" dir="ltr">{{ $email }}</a>
                    </li>
                @endif
                @if ($phone = $site->get('contact_phone'))
                    <li class="flex items-center gap-3">
                        <x-ui.icon name="phone" class="size-5 text-gold-400" />
                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="transition hover:text-white" dir="ltr">{{ $phone }}</a>
                    </li>
                @endif
                @if ($address = $site->tr('address'))
                    <li class="flex items-center gap-3">
                        <x-ui.icon name="pin" class="size-5 text-gold-400" />
                        <span>{{ $address }}</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    @if ($credentials = $site->credentials())
        <div class="relative border-t border-white/10">
            <div class="container-site flex flex-wrap items-center gap-x-8 gap-y-3 py-5 text-sm text-white/60">
                <p class="flex items-center gap-2 font-medium text-white">
                    <x-ui.icon name="shield" class="size-4 text-gold-400" /> {{ __('footer.credentials') }}
                </p>
                @foreach ($credentials as $credential)
                    <a @if ($credential['url']) href="{{ $credential['url'] }}" target="_blank" rel="noopener" @endif class="inline-flex items-center gap-2 transition hover:text-white">
                        @if ($credential['logo'])
                            <img src="{{ Storage::disk('public')->url($credential['logo']) }}" alt="" class="h-6 w-auto rounded-sm bg-white p-0.5" loading="lazy">
                        @endif
                        <span>{{ $credential['name'] }}</span>
                        @if ($credential['number'])
                            <span class="font-latin text-white/40" dir="ltr">{{ $credential['number'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="relative border-t border-white/10">
        <div class="container-site flex flex-col gap-3 py-6 text-sm text-white/50 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $site->tr('site_name') }}. {{ __('footer.rights') }}</p>
            <div class="flex gap-6">
                <a href="{{ route('privacy-policy') }}" class="transition hover:text-white">{{ __('footer.privacy') }}</a>
                <a href="{{ route('terms-and-conditions') }}" class="transition hover:text-white">{{ __('footer.terms') }}</a>
            </div>
        </div>
    </div>
</footer>
