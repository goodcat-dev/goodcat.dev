import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Bricolage Grotesque', {
                    alias: 'grotesk',
                    variable: '--font-bricolage-grotesque',
                    weights: [400, 500, 700, 800],
                }),
                bunny('JetBrains Mono', {
                    alias: 'mono',
                    variable: '--font-jetbrains-mono',
                    weights: [400, 500],
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
