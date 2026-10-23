@extends('layouts.app')

@section('title', __('contact.meta_title'))
@section('description', __('contact.text'))

@section('content')
    <x-ui.page-hero :eyebrow="__('contact.eyebrow')" :title="__('contact.title')" :text="__('contact.text')" />

    <section id="contact" class="py-24 sm:py-32">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <div class="space-y-4 lg:col-span-4">
                @foreach ([
                    ['icon' => 'phone', 'label' => __('contact.phone'), 'value' => $site->get('contact_phone'), 'href' => 'tel:'.preg_replace('/\s+/', '', (string) $site->get('contact_phone')), 'ltr' => true],
                    ['icon' => 'whatsapp', 'label' => __('contact.whatsapp'), 'value' => $site->get('whatsapp'), 'href' => $site->whatsappUrl(__('contact.whatsapp_message')), 'ltr' => true],
                    ['icon' => 'mail', 'label' => __('contact.email'), 'value' => $site->get('contact_email'), 'href' => 'mailto:'.$site->get('contact_email'), 'ltr' => true],
                    ['icon' => 'pin', 'label' => __('contact.address'), 'value' => $site->tr('address'), 'href' => $site->mapLinkUrl(), 'ltr' => false],
                ] as $item)
                    @continue(blank($item['value']))
                    <div class="flex items-start gap-5 rounded-sm border border-ink-900/5 bg-white p-6" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                        <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-sm bg-brand-700 text-white">
                            <x-ui.icon :name="$item['icon']" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-ink-700">{{ $item['label'] }}</p>
                            @if ($item['href'])
                                <a href="{{ $item['href'] }}" @if (in_array($item['icon'], ['pin', 'whatsapp'])) target="_blank" rel="noopener" @endif class="mt-1 block truncate text-lg font-medium transition hover:text-brand-700" @if ($item['ltr']) dir="ltr" @endif>{{ $item['value'] }}</a>
                            @else
                                <p class="mt-1 text-lg font-medium">{{ $item['value'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if ($nationalAddress = $site->nationalAddressLines())
                    <div class="flex items-start gap-5 rounded-sm border border-ink-900/5 bg-white p-6" data-reveal>
                        <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-sm bg-brand-700 text-white">
                            <x-ui.icon name="building" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-ink-700">{{ __('contact.national_address') }}</p>
                            <address class="mt-1 space-y-0.5 text-lg font-medium not-italic">
                                @foreach ($nationalAddress as $line)
                                    <span class="block">{{ $line }}</span>
                                @endforeach
                            </address>
                        </div>
                    </div>
                @endif

                @if ($whatsappUrl = $site->whatsappUrl(__('contact.whatsapp_message')))
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" data-track="whatsapp_click" class="flex items-center justify-center gap-3 rounded-sm bg-[#25d366] p-4 text-lg font-medium text-white transition hover:bg-[#1ebe5b]" data-reveal>
                        <x-ui.icon name="whatsapp" class="size-6" /> {{ __('contact.whatsapp_cta') }}
                    </a>
                @endif

                <div class="rounded-sm bg-forest-950 p-6 text-white" data-reveal>
                    <p class="text-sm font-semibold text-white/60">{{ __('contact.follow') }}</p>
                    <div class="mt-4 flex gap-3">
                        @if ($linkedin = $site->get('linkedin_url'))
                            <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-full border border-white/15 transition hover:border-brand-400 hover:text-gold-400" aria-label="LinkedIn"><x-ui.icon name="linkedin" class="size-5" /></a>
                        @endif
                        @if ($x = $site->get('x_url'))
                            <a href="{{ $x }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-full border border-white/15 transition hover:border-brand-400 hover:text-gold-400" aria-label="X"><x-ui.icon name="x" class="size-4" /></a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8" data-reveal data-reveal-delay="0.1">
                <div class="rounded-sm border border-ink-900/5 bg-white p-6 sm:p-10">
                    <livewire:contact-form />
                </div>
            </div>
        </div>
    </section>

    @if ($mapEmbed = $site->mapEmbedUrl())
        <section class="pb-24 sm:pb-32">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <h2 class="heading-display text-3xl" data-reveal>{{ __('contact.map_title') }}</h2>
                    <a href="{{ $site->mapLinkUrl() }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-brand-700 transition hover:text-brand-600">
                        <x-ui.icon name="pin" class="size-5" /> {{ __('contact.open_map') }}
                    </a>
                </div>
                <div class="mt-8 overflow-hidden rounded-sm border border-ink-900/10" data-reveal>
                    <iframe src="{{ $mapEmbed }}" title="{{ __('contact.map_title') }}" class="block h-[26rem] w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
            </div>
        </section>
    @endif
@endsection
