import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0', // Isso expõe para todas as interfaces (IPs)
        hmr: {
            host: '172.29.254.116' // COLOQUE O IP DO SEU WINDOWS AQUI
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
