@extends('layouts.app')

@section('title', $service->tr('meta_title') ?: $service->tr('title'))
@section('description', $service->tr('meta_description') ?: $service->tr('excerpt'))

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Service',
                    'name' => $service->tr('title'),
                    'description' => $service->tr('excerpt'),
                    'url' => url()->current(),
                    'image' => $service->imageUrl(),
                    'serviceType' => $service->tr('title'),
                    'areaServed' => ['@type' => 'Country', 'name' => 'Saudi Arabia'],
                    'inLanguage' => app()->getLocale(),
                    'provider' => ['@type' => 'ProfessionalService', 'name' => $site->tr('site_name', 'en'), 'url' => url('/')],
                    'hasOfferCatalog' => ($highlights = $service->trList('highlights')) ? [
                        '@type' => 'OfferCatalog',
                        'name' => $service->tr('title'),
                        'itemListElement' => collect($highlights)->map(fn ($highlight) => ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => $highlight]])->all(),
                    ] : null,
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => $site->tr('site_name'), 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => __('header.services'), 'item' => route('services.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $service->tr('title'), 'item' => url()->current()],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <x-ui.page-hero :eyebrow="__('services.eyebrow')" :title="$service->tr('title')" :text="$service->tr('excerpt')" :image="$service->imageUrl()" />

    <section class="py-24 sm:py-32">
        <div class="container-site grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-8">
                @if ($service->image)
                    <img src="{{ Storage::disk('public')->url($service->image) }}" alt="" class="mb-12 aspect-[16/9] w-full rounded-sm object-cover" data-reveal>
                @endif

                <div class="prose-site" data-reveal>{!! $service->tr('body') !!}</div>

                @if ($highlights = $service->trList('highlights'))
                    <h2 class="mt-16 heading-display text-3xl" data-reveal>{{ __('services.highlights') }}</h2>
                    <ul class="mt-8 grid gap-4 sm:grid-cols-2">
                        @foreach ($highlights as $highlight)
                            <li class="flex items-start gap-4 rounded-sm border border-ink-900/5 bg-white p-6" data-reveal data-reveal-delay="{{ $loop->index * 0.05 }}">
                                <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-700 text-white">
                                    <x-ui.icon name="check" class="size-4" />
                                </span>
                                <span class="font-semibold leading-relaxed">{{ $highlight }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <aside class="lg:col-span-4">
                <div class="sticky top-28 space-y-5">
                    <div class="relative overflow-hidden rounded-sm bg-forest-950 p-8 text-white">
                        <div class="pattern-star absolute inset-0 text-white/[0.03]" aria-hidden="true"></div>
                        <div class="relative">
                            <h2 class="heading-display text-3xl">{{ __('services.sidebar_title') }}</h2>
                            <p class="mt-3 leading-relaxed text-white/70">{{ __('services.sidebar_text') }}</p>
                            <x-ui.button :href="route('request-service', ['service' => $service->slug])" class="mt-8 w-full">{{ __('global.actions.request_this_service') }}</x-ui.button>
                        </div>
                    </div>

                    @if ($otherServices->isNotEmpty())
                        <nav class="rounded-sm border border-ink-900/5 bg-white p-8" aria-label="{{ __('services.other') }}">
                            <p class="text-sm font-medium text-ink-700">{{ __('services.other') }}</p>
                            <ul class="mt-4 divide-y divide-ink-900/5">
                                @foreach ($otherServices as $other)
                                    <li>
                                        <a href="{{ route('services.show', $other) }}" class="group flex items-center justify-between gap-4 py-4 font-medium transition hover:text-brand-700">
                                            {{ $other->tr('title') }}
                                            <x-ui.icon name="arrow" class="size-4 text-ink-900/30 transition group-hover:text-brand-700 rtl:-scale-x-100" />
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </nav>
                    @endif
                </div>
            </aside>
        </div>
    </section>

    @if ($caseStudies->isNotEmpty())
        <section class="bg-sand-50 py-20 sm:py-28">
            <div class="container-site">
                <x-ui.section-heading :eyebrow="__('impact.eyebrow')" :title="__('impact.home_title')" />
                <div class="mt-14 grid gap-8 md:grid-cols-2">
                    @foreach ($caseStudies as $caseStudy)
                        <x-ui.case-study-card :case-study="$caseStudy" data-reveal />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($insights->isNotEmpty())
        <section class="py-20 sm:py-28">
            <div class="container-site">
                <x-ui.section-heading :eyebrow="__('insights.eyebrow')" :title="__('insights.related')" />
                <div class="mt-14 grid gap-x-8 gap-y-14 md:grid-cols-3">
                    @foreach ($insights as $insight)
                        <x-ui.insight-card :insight="$insight" data-reveal data-reveal-delay="{{ $loop->index * 0.1 }}" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
