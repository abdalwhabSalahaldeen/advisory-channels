@extends('layouts.app')

@section('title', $doc->tr('title'))

@section('content')
    <x-ui.page-hero :title="$doc->tr('title')">
        <p class="mt-6 text-sm text-white/50">{{ $doc->updated_at->locale(app()->getLocale())->translatedFormat('j F Y') }}</p>
    </x-ui.page-hero>

    <section class="py-20 sm:py-28">
        <div class="container-site max-w-3xl!">
            <div class="prose-site">{!! $doc->tr('body') !!}</div>
        </div>
    </section>
@endsection
