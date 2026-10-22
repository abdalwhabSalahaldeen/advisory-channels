<footer class="relative overflow-hidden bg-ink-950 text-white">
    <div class="grid-lines absolute inset-0 opacity-60" aria-hidden="true"></div>

    <div class="container-site relative grid gap-12 py-16 lg:grid-cols-12 lg:py-20">
        <div class="lg:col-span-5">
            <x-site.logo dark />
            <p class="mt-6 max-w-md leading-relaxed text-white/60">{{ __('site.footer.about') }}</p>

            <div class="mt-8 flex gap-3">
                @if ($linkedin = $site->get('linkedin_url'))
                    <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-full border border-white/15 text-white/70 transition hover:border-brand-400 hover:text-brand-400" aria-label="LinkedIn">
                        <x-ui.icon name="linkedin" class="size-5" />
                    </a>
                @endif
                @if ($x = $site->get('x_url'))
                    <a href="{{ $x }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-full border border-white/15 text-white/70 transition hover:border-brand-400 hover:text-brand-400" aria-label="X">
                        <x-ui.icon name="x" class="size-4" />
                    </a>
                @endif
            </div>
        </div>

        <div class="lg:col-span-3">
            <p class="text-sm font-bold text-white">{{ __('site.footer.explore') }}</p>
            <ul class="mt-5 space-y-3 text-white/60">
                <li><a href="{{ route('about') }}" class="transition hover:text-white">{{ __('site.nav.about') }}</a></li>
                <li><a href="{{ route('services.index') }}" class="transition hover:text-white">{{ __('site.nav.services') }}</a></li>
                <li><a href="{{ route('saudi-gate') }}" class="transition hover:text-white">{{ __('site.nav.saudi_gate') }}</a></li>
                <li><a href="{{ route('careers.index') }}" class="transition hover:text-white">{{ __('site.nav.careers') }}</a></li>
                <li><a href="{{ route('request-service') }}" class="transition hover:text-white">{{ __('site.nav.request_service') }}</a></li>
            </ul>
        </div>

        <div class="lg:col-span-4">
            <p class="text-sm font-bold text-white">{{ __('site.footer.contact') }}</p>
            <ul class="mt-5 space-y-4 text-white/60">
                @if ($email = $site->get('contact_email'))
                    <li class="flex items-center gap-3">
                        <x-ui.icon name="mail" class="size-5 text-brand-400" />
                        <a href="mailto:{{ $email }}" class="transition hover:text-white" dir="ltr">{{ $email }}</a>
                    </li>
                @endif
                @if ($phone = $site->get('contact_phone'))
                    <li class="flex items-center gap-3">
                        <x-ui.icon name="phone" class="size-5 text-brand-400" />
                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="transition hover:text-white" dir="ltr">{{ $phone }}</a>
                    </li>
                @endif
                @if ($address = $site->tr('address'))
                    <li class="flex items-center gap-3">
                        <x-ui.icon name="pin" class="size-5 text-brand-400" />
                        <span>{{ $address }}</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="relative border-t border-white/10">
        <div class="container-site flex flex-col gap-3 py-6 text-sm text-white/50 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $site->tr('site_name') }}. {{ __('site.footer.rights') }}</p>
            <a href="{{ route('privacy') }}" class="transition hover:text-white">{{ __('site.footer.privacy') }}</a>
        </div>
    </div>
</footer>
