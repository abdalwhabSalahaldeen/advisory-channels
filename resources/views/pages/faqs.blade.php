@extends('layouts.app')

@section('title', __('faqs.meta_title'))
@section('description', __('faqs.text'))

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'inLanguage' => app()->getLocale(),
            'mainEntity' => $categories->flatMap->faqs->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq->tr('question'),
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->tr('answer')],
            ])->values()->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <x-ui.page-hero :eyebrow="__('faqs.eyebrow')" :title="__('faqs.title')" :text="__('faqs.text')" />

    <section class="py-24 sm:py-32" x-data="{ category: 'all' }">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <nav class="lg:col-span-3" aria-label="{{ __('faqs.title') }}">
                <ul class="flex gap-2 overflow-x-auto pb-2 lg:sticky lg:top-28 lg:flex-col lg:overflow-visible">
                    <li>
                        <button type="button" @click="category = 'all'" :class="category === 'all' ? 'bg-brand-700 text-white' : 'bg-white text-ink-700 hover:text-brand-700'" class="w-full whitespace-nowrap rounded-full px-5 py-3 text-start text-sm font-medium transition">{{ __('faqs.all') }}</button>
                    </li>
                    @foreach ($categories as $category)
                        <li>
                            <button type="button" @click="category = @js($category->slug)" :class="category === @js($category->slug) ? 'bg-brand-700 text-white' : 'bg-white text-ink-700 hover:text-brand-700'" class="w-full whitespace-nowrap rounded-full px-5 py-3 text-start text-sm font-medium transition">{{ $category->tr('name') }}</button>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="space-y-14 lg:col-span-9">
                @foreach ($categories as $category)
                    <div x-show="category === 'all' || category === @js($category->slug)">
                        <h2 class="mb-4 heading-display text-3xl">{{ $category->tr('name') }}</h2>
                        <x-ui.faq-list :faqs="$category->faqs" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-ui.cta-band :title="__('faqs.still_title')" :text="__('faqs.still_text')">
        <x-ui.button :href="route('contact')" variant="light">{{ __('header.contact') }}</x-ui.button>
    </x-ui.cta-band>
@endsection
