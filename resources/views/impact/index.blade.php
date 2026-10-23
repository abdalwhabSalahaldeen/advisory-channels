@extends('layouts.app')

@section('title', __('impact.meta_title'))
@section('description', __('impact.text'))

@section('content')
    <x-ui.page-hero :image="asset(config('site.images.pages.impact'))" :eyebrow="__('impact.eyebrow')" :title="__('impact.title')" :text="__('impact.text')" />

    <section class="py-20 sm:py-28">
        <p class="container-site mb-12 text-sm text-ink-700/70">{{ __('impact.confidential') }}</p>
        <div class="container-site grid gap-8 md:grid-cols-2">
            @foreach ($caseStudies as $caseStudy)
                <x-ui.case-study-card :case-study="$caseStudy" data-reveal data-reveal-delay="{{ ($loop->index % 2) * 0.1 }}" />
            @endforeach
        </div>
    </section>

    <x-ui.cta-band :title="__('index.cta_title')" :text="__('index.cta_text')">
        <x-ui.button :href="route('request-service')" variant="gold">{{ __('header.request_service') }}</x-ui.button>
    </x-ui.cta-band>
@endsection
