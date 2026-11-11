import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import path from "path";

export default defineConfig({
    resolve: {
        alias: {
            "@scripts": path.resolve(import.meta.dirname, "./resources/js/"),
            "@pages": path.resolve(import.meta.dirname, "./resources/js/Pages/vue/"),
            "@components": path.resolve(import.meta.dirname, "./resources/js/Pages/vue/components/"),
            "@modals": path.resolve(import.meta.dirname, "./resources/js/Pages/vue/components/modals/"),
            "@shops": path.resolve(import.meta.dirname, "./resources/js/Pages/vue/components/modals/shops/"),
            "@alerts": path.resolve(import.meta.dirname, "./resources/js/Pages/vue/components/alerts/")
        }
    },
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                }
            }
        }),
    ],
});
