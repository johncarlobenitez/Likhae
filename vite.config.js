import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin/admin.css',
                'resources/css/Buyer/buyer.css',
                'resources/css/seller/seller.css',
                'resources/js/app.js',
                'resources/js/admin/admin.js',
                'resources/js/logistics.js',
                'resources/js/rider.js',
            ],
            refresh: [
                'resources/views/**',
                'routes/**',
                'app/Http/**',
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: false,
    },
    build: {
        manifest: true,
        emptyOutDir: true,
    },
});