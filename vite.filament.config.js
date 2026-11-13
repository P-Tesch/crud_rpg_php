import { defineConfig } from 'vite';

/**
 * Builds the custom Filament Alpine components (async-loaded via FilamentAsset, not part of the Inertia app).
 * Output is committed and published to public/ with `php artisan filament:assets`.
 */
export default defineConfig({
    publicDir: false,
    define: {
        'process.env.NODE_ENV': JSON.stringify('production'),
    },
    build: {
        outDir: 'resources/js/dist/filament',
        emptyOutDir: false,
        minify: true,
        rolldownOptions: {
            // Library mode keeps whitespace by default; this file is only ever served to browsers.
            output: { minify: true },
        },
        lib: {
            entry: 'resources/js/filament/lua-code-editor.js',
            formats: ['es'],
            fileName: () => 'lua-code-editor.js',
        },
    },
});
