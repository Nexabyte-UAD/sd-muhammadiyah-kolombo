<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();
        $settingsArray = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settingsArray = \App\Models\Setting::pluck('value', 'key')->toArray();
            }
        } catch (\Exception $e) {
            // Abaikan jika database belum siap/migrate
        }
        \Illuminate\Support\Facades\View::share('settings', $settingsArray);
    }
}
