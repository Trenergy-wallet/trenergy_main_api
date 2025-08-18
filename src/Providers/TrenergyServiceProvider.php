<?php

namespace Apd\Trenergy\Providers;

use Apd\Trenergy\Services\TrenergyService;
use Illuminate\Support\ServiceProvider;

class TrenergyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('trenergy.service', function () {
            return new TrenergyService();
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/trenergy.php', 'trenergy'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/trenergy.php' => config_path('trenergy.php'),
        ]);
    }
}
