import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            // Abaikan folder non-frontend agar watcher tidak menyentuh berkas
            // data/backup besar (mis. database/data/*.json) yang bisa ter-lock
            // sehingga memicu EBUSY dan mematikan dev server.
            ignored: [
                '**/storage/**',
                '**/database/**',
                '**/vendor/**',
                '**/node_modules/**',
                '**/public/build/**',
                '**/.git/**',
            ],
        },
    },
});
