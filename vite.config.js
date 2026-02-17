import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],

    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },

    build: {
        outDir: 'public/build',
        manifest: 'manifest.json',
        minify: 'terser',
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['vue', '@inertiajs/vue3'],
                },
            },
        },
    },

    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: false,
        hmr: {
            host: 'localhost',
            port: 5173,
            protocol: 'http',
        },
    },
})
