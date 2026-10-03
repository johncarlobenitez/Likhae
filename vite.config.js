import os from 'node:os';
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

function getLanAddress() {
    for (const interfaces of Object.values(os.networkInterfaces())) {
        const address = interfaces?.find((candidate) =>
            candidate.family === 'IPv4'
            && !candidate.internal
            && (
                candidate.address.startsWith('10.')
                || candidate.address.startsWith('192.168.')
                || /^172\.(1[6-9]|2\d|3[01])\./.test(candidate.address)
            )
        );

        if (address) {
            return address.address;
        }
    }

    return 'localhost';
}

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/css/admin/admin.css',
                    'resources/css/Buyer/buyer.css',
                    'resources/css/seller/seller.css',
                    'resources/css/auth/login.css',
                    'resources/css/auth/register.css',
                    'resources/css/logistic/app.css',
                    'resources/css/logistic/landing.css',
                    'resources/js/app.js',
                    'resources/js/Buyer/buyer.js',
                    'resources/js/admin/admin.js',
                    'resources/js/seller/seller.js',
                    'resources/js/auth/login.js',
                    'resources/js/auth/register.js',
                    'resources/js/logistic/app.js',
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
            host: '0.0.0.0',
            port: 5173,
            strictPort: false,
            hmr: {
                host: env.VITE_HMR_HOST || getLanAddress(),
            },
        },
        build: {
            manifest: 'manifest.json',
            emptyOutDir: true,
        },
    };
});
