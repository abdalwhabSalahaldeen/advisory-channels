import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                // Arabic: the typeface of the Saudi Royal Commission sites.
                bunny('IBM Plex Sans Arabic', {
                    alias: 'plex-arabic',
                    weights: [300, 400, 500, 600, 700],
                    subsets: ['arabic', 'latin'],
                    preload: [{ weight: 300 }, { weight: 400 }],
                }),
                bunny('IBM Plex Sans', {
                    alias: 'plex',
                    weights: [300, 400, 500, 600, 700],
                    preload: [{ weight: 400 }],
                }),
                // English headlines get an editorial serif, as on the big consulting firms' sites.
                bunny('Fraunces', {
                    alias: 'fraunces',
                    weights: [300, 400, 500],
                    styles: ['normal', 'italic'],
                    preload: [{ weight: 300, style: 'normal' }],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
