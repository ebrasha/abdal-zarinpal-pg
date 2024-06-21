<?php

namespace Abdal\AbdalZarinpalPg;

use Illuminate\Support\ServiceProvider;

class ZarinpalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // بارگذاری پیکربندی
        $this->mergeConfigFrom(
            __DIR__.'/../config/abdal-zarinpal-pg.php', 'abdal-zarinpal-pg'
        );

        // ثبت سرویس singleton
        $this->app->singleton('zarinpal', function () {
            return new Zarinpal();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // انتشار پیکربندی
        $this->publishes([
            __DIR__.'/../config/abdal-zarinpal-pg.php' => config_path('abdal-zarinpal-pg.php'),
        ], 'config');
    }
}
