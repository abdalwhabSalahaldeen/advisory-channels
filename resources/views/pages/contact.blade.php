@extends('layouts.app')

@section('title', __('contact.meta_title'))
@section('description', __('contact.text'))

@section('content')
    <x-ui.page-hero :eyebrow="__('contact.eyebrow')" :title="__('contact.title')" :text="__('contact.text')" />

    <section id="contact" class="py-24 sm:py-32">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <div class="space-y-4 lg:col-span-4">
                @foreach ([
                    ['icon' => 'mail', 'label' => __('contact.email'), 'value' => $site->get('contact_email'), 'href' => 'mailto:'.$site->get('contact_email'), 'ltr' => true],
                    ['icon' => 'phone', 'label' => __('contact.phone'), 'value' => $site->get('contact_phone'), 'href' => 'tel:'.preg_replace('/\s+/', '', (string) $site->get('contact_phone')), 'ltr' => true],
                    ['icon' => 'pin', 'label' => __('contact.address'), 'value' => $site->tr('address'), 'href' => $site->get('map_url'), 'ltr' => false],
                ] as $item)
                    @continue(blank($item['value']))
                    <div class="flex items-start gap-5 rounded-3xl border border-ink-900/5 bg-white p-6" data-reveal data-reveal-delay="{{ $loop->index * 0.08 }}">
                        <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-brand-700 text-white">
                            <x-ui.icon :name="$item['icon']" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-ink-700">{{ $item['label'] }}</p>
                            @if ($item['href'])
                                <a href="{{ $item['href'] }}" @if ($item['icon'] === 'pin') target="_blank" rel="noopener" @endif class="mt-1 block truncate text-lg font-bold transition hover:text-brand-700" @if ($item['ltr']) dir="ltr" @endif>{{ $item['value'] }}</a>
                            @else
                                <p class="mt-1 text-lg font-bold">{{ $item['value'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="rounded-3xl bg-ink-950 p-6 text-white" data-reveal>
                    <p class="text-sm font-semibold text-white/60">{{ __('contact.follow') }}</p>
                    <div class="mt-4 flex gap-3">
                        @if ($linkedin = $site->get('linkedin_url'))
                            <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-full border border-white/15 transition hover:border-brand-400 hover:text-brand-400" aria-label="LinkedIn"><x-ui.icon name="linkedin" class="size-5" /></a>
                        @endif
                        @if ($x = $site->get('x_url'))
                            <a href="{{ $x }}" target="_blank" rel="noopener" class="inline-flex size-11 items-center justify-center rounded-full border border-white/15 transition hover:border-brand-400 hover:text-brand-400" aria-label="X"><x-ui.icon name="x" class="size-4" /></a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8" data-reveal data-reveal-delay="0.1">
                <div class="rounded-3xl border border-ink-900/5 bg-white p-6 sm:p-10">
                    <livewire:contact-form />
                </div>
            </div>
        </div>
    </section>
@endsection
