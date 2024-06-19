<?php

namespace Abdal\AbdalZarinpalPg;

use Illuminate\Support\ServiceProvider;

class ZarinpalServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/zarinpal.php' => config_path('zarinpal.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/zarinpal.php', 'zarinpal'
        );

        $this->app->singleton('zarinpal', function () {
            return new Zarinpal();
        });
    }
}
