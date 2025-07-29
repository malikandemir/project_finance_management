<?php

namespace App\Providers;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

class FilamentLanguageSwitcherServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the language switcher view component
        Blade::component('language-switcher', \App\View\Components\LanguageSwitcher::class);

        // Add the language switcher to Filament's topbar
        FilamentView::registerRenderHook(
            'panels::topbar.end',
            fn (): string => Blade::render('@include("components.language-switcher")')
        );
    }
}
