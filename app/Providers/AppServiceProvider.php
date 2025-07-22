<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(VendorValidationService::class, function ($app) {
            return new VendorValidationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // DB::listen(function ($query) {
        //     dump($query->sql);
        //     dump($query->bindings);
        // });
    }
}
