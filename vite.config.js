import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Tajawal, the brand typeface for Arabic and English; downloaded at build time and self-hosted.
            fonts: [
                bunny('Tajawal', {
                    alias: 'tajawal',
                    weights: [300, 400, 500, 700, 800],
                    subsets: ['arabic', 'latin'],
                    preload: [{ weight: 300 }, { weight: 400 }, { weight: 700 }],
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
