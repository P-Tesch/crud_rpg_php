import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    resolve: {
        alias: {
            "@scripts": path.resolve(__dirname, "./resources/js/"),
            "@pages": path.resolve(__dirname, "./resources/js/Pages/vue/"),
            "@components": path.resolve(__dirname, "./resources/js/Pages/vue/components/"),
            "@modals": path.resolve(__dirname, "./resources/js/Pages/vue/components/modals/"),
            "@shops": path.resolve(__dirname, "./resources/js/Pages/vue/components/modals/shops/"),
            "@alerts": path.resolve(__dirname, "./resources/js/Pages/vue/components/alerts/")
        }
    },
    plugins: [
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
