import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                 'resources/js/app.js',
                 'resources/js/report-export.js',
                 'resources/js/matrix-grading.js',
                'resources/js/training-schedule.js'],
            refresh: true,
        }),
    ],
});


