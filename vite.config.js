import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Auth (login + register)
                'resources/css/auth/login.css',
                'resources/css/auth/register.css',
                'resources/js/auth/login.js',
                'resources/js/auth/register.js',

                // Buyer & Guest
                'resources/css/Buyer/buyer.css',
                'resources/js/buyer/buyer.js',

                // Admin
                'resources/css/admin/admin.css',
                'resources/js/admin/admin.js',

                // Seller
                'resources/css/seller/seller.css',
                'resources/js/seller/seller.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
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



