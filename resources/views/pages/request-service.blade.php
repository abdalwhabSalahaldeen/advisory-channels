@extends('layouts.app')

@section('title', __('request.meta_title'))
@section('description', __('request.text'))

@section('content')
    <x-ui.page-hero :eyebrow="__('request.eyebrow')" :title="__('request.title')" :text="__('request.text')" />

    <section class="py-24 sm:py-32">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-8" data-reveal>
                <div class="rounded-sm border border-ink-900/5 bg-white p-6 sm:p-10">
                    <livewire:request-service-form :preselect="$service" />
                </div>
            </div>

            <aside class="lg:col-span-4" data-reveal data-reveal-delay="0.1">
                <div class="sticky top-28 rounded-sm bg-forest-950 p-8 text-white">
                    <h2 class="text-xl font-medium">{{ __('request.next_title') }}</h2>
                    <ol class="mt-8 space-y-6">
                        @foreach (__('request.next_steps') as $nextStep)
                            <li class="flex gap-4">
                                <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full border border-gold-400/40 font-latin font-medium text-gold-400">{{ $loop->iteration }}</span>
                                <span class="pt-1.5 leading-relaxed text-white/75">{{ $nextStep }}</span>
                            </li>
                        @endforeach
                    </ol>
                    @if ($email = $site->get('contact_email'))
                        <a href="mailto:{{ $email }}" class="mt-10 flex items-center gap-3 border-t border-white/10 pt-6 text-sm font-semibold text-white/70 transition hover:text-white">
                            <x-ui.icon name="mail" class="size-5 text-gold-400" />
                            <span dir="ltr">{{ $email }}</span>
                        </a>
                    @endif
                </div>
            </aside>
        </div>
    </section>
@endsection
