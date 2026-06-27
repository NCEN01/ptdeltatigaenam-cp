import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            includeAssets: ['favicon.svg', 'icons/icon-192.png', 'icons/icon-512.png'],
            manifest: {
                name: 'PT Delta Tiga Enam',
                short_name: 'Delta Tiga Enam',
                description: 'Human capital, corporate training & professional certification.',
                start_url: '/',
                scope: '/',
                display: 'standalone',
                background_color: '#0A2A5E',
                theme_color: '#0A2A5E',
                lang: 'id',
                icons: [
                    { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
                    { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' },
                    { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' },
                ],
            },
            workbox: {
                globPatterns: ['**/*.{js,css,svg,woff2}'],
                navigateFallback: null,
                runtimeCaching: [
                    {
                        urlPattern: ({ url }) => url.pathname.startsWith('/storage/'),
                        handler: 'StaleWhileRevalidate',
                        options: { cacheName: 'media' },
                    },
                ],
            },
            devOptions: { enabled: false },
        }),
    ],
});
