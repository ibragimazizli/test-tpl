import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/style.min.css',
                'resources/js/app.js',
                'resources/js/main.js',
                'resources/js/api-security.js',
                'resources/scss/style.scss',
            ],
            refresh: true,
        }),
        vue(),
    ],
});
