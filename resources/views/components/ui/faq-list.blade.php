@props(['faqs'])

{{-- Accessible accordion: one answer open at a time. --}}
<div x-data="{ open: null }" {{ $attributes->merge(['class' => 'divide-y divide-ink-900/10 border-y border-ink-900/10']) }}>
    @foreach ($faqs as $faq)
        <div>
            <h3>
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-6 py-6 text-start text-lg font-bold text-ink-900 transition hover:text-brand-700"
                    @click="open = open === {{ $faq->getKey() }} ? null : {{ $faq->getKey() }}"
                    :aria-expanded="(open === {{ $faq->getKey() }}).toString()"
                    aria-controls="faq-{{ $faq->getKey() }}"
                >
                    <span>{{ $faq->tr('question') }}</span>
                    <span class="relative inline-flex size-9 shrink-0 items-center justify-center rounded-full border border-ink-900/10 transition" :class="open === {{ $faq->getKey() }} && 'rotate-45 border-brand-600 bg-brand-700 text-white'" aria-hidden="true">
                        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" /></svg>
                    </span>
                </button>
            </h3>
            <div id="faq-{{ $faq->getKey() }}" x-show="open === {{ $faq->getKey() }}" x-collapse x-cloak>
                <p class="pb-6 text-lg leading-relaxed text-ink-700">{{ $faq->tr('answer') }}</p>
            </div>
        </div>
    @endforeach
</div>
