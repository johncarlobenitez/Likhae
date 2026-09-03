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
                'resources/js/buyer/registration.js',

                // Guest
                'resources/css/Guest/home.css',
                'resources/css/Guest/products.css',
                'resources/css/Guest/product-details.css',

                // Buyer
                'resources/css/Buyer/app.css',
                'resources/css/Buyer/registration.css',
                'resources/js/buyer/app.js',
                'resources/css/Buyer/home.css',
                'resources/css/Buyer/products.css',
                'resources/css/Buyer/product-details.css',
                'resources/css/Buyer/cart.css',
                'resources/css/Buyer/checkout.css',
                'resources/css/Buyer/orders.css',
                'resources/css/Buyer/order-details.css',
                'resources/css/Buyer/order-success.css',
                'resources/css/Buyer/messages.css',
                'resources/css/Buyer/profile.css',
                'resources/css/Buyer/addresses.css',
                'resources/css/Buyer/review.css',
                'resources/css/Buyer/notifications.css',
                'resources/css/Buyer/wishlist.css',
                'resources/css/Buyer/flash-deals.css',
                'resources/css/Buyer/local-finds.css',
                'resources/css/Buyer/security.css',

                // Admin
                'resources/css/admin/app.css',
                'resources/js/admin/app.js',

                // Seller
                'resources/css/seller/app.css',
                'resources/js/seller/app.js',
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
