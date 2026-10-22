@extends('layouts.app')

@section('title', __('careers.general_application'))

@section('content')
    <x-ui.page-hero :eyebrow="__('careers.eyebrow')" :title="__('careers.general_title')" :text="__('careers.general_text')" />

    <section class="py-24 sm:py-32">
        <div class="container-site max-w-3xl!">
            <div class="rounded-sm border border-ink-900/5 bg-white p-6 sm:p-10" data-reveal>
                <livewire:job-application-form />
            </div>
        </div>
    </section>
@endsection
