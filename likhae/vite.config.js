import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Global
                'resources/js/app.js',

                // Buyer
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

                // Guest
                'resources/css/Guest/home.css',
                'resources/css/Guest/products.css',
                'resources/css/Guest/product-details.css',
                'resources/css/Guest/auth/login.css',
                'resources/css/Guest/auth/register.css',
                'resources/css/Guest/register.css',
                'resources/js/auth/register.js',
                'resources/js/guest/auth/register.js',

                // Admin
                'resources/css/admin/app.css',

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
