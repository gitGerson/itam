<?php

namespace App\Providers;

use App\Services\MenuService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        if (app()->hasDebugModeEnabled()) {
            Blade::anonymousComponentNamespace(
                'laravel-exceptions-renderer::components',
                'laravel-exceptions-renderer'
            );
        }

        View::composer('layouts.sneat', function ($view): void {
            $view->with(
                'searchMenuItems',
                app(MenuService::class)->itemsForContext('search', auth()->user())
            );
        });
    }
}
