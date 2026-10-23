{{--
    Google Analytics 4, loaded only when a measurement ID is set in Site settings > SEO.
    Tracked conversions: form submissions (Livewire "track" events), WhatsApp, phone and email clicks.
--}}
@if ($measurementId = $site->get('ga_measurement_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($measurementId) }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', @js($measurementId), { content_group: @js(request()->route()?->getName()), language: @js(app()->getLocale()) });

        document.addEventListener('livewire:init', () => {
            Livewire.on('track', ({ name, params }) => gtag('event', name, { ...params, page_path: location.pathname }));
        });

        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (! link) return;

            const href = link.getAttribute('href');
            const name = link.dataset.track
                || (href.startsWith('tel:') && 'phone_click')
                || (href.startsWith('mailto:') && 'email_click')
                || (/^https:\/\/wa\.me\//.test(href) && 'whatsapp_click');

            if (name) gtag('event', name, { link_url: href, page_path: location.pathname });
        });
    </script>
@endif
