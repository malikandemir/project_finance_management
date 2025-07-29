<?php

namespace App\Providers;

use App\Http\Middleware\SetLocale;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;

class LocaleServiceProvider extends ServiceProvider
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
    public function boot(Kernel $kernel): void
    {
        $kernel->pushMiddleware(SetLocale::class);
    }
}
