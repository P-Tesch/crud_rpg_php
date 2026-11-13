<?php

namespace App\Providers;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $luaCodeEditor = resource_path("js/dist/filament/lua-code-editor.js");

        FilamentAsset::register([
            AlpineComponent::make("lua-code-editor", $luaCodeEditor),
        ]);

        FilamentAsset::appVersion(hash_file("xxh128", $luaCodeEditor) ?: null);
    }
}
