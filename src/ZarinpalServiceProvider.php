<?php

/*
 **********************************************************************
 * -------------------------------------------------------------------
 * Project Name : Abdal Zarinpal PG
 * File Name    : ZarinpalServiceProvider.php
 * Author       : Ebrahim Shafiei (EbraSha)
 * Email        : Prof.Shafiei@Gmail.com
 * Created On   : 2024-06-21
 * Description  : Abdal Zarinpal PG Service Provider
 * -------------------------------------------------------------------
 *
 * "Coding is an engaging and beloved hobby for me. I passionately and insatiably pursue knowledge in cybersecurity and programming."
 * – Ebrahim Shafiei
 *
 **********************************************************************
 */

namespace Abdal\AbdalZarinpalPg;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ZarinpalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() : void
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
    public function boot(): void
    {
        if (Cache::get("ZARINPAL_MERCHANT_ID") == "") {
            $expiresAt = Carbon::now()->addYear(10);
            Cache::put('ZARINPAL_MERCHANT_ID', env('ZARINPAL_MERCHANT_ID', '00000000-0000-0000-0000-000000000000'), $expiresAt);
        }

        if (Cache::get("ZARINPAL_CURRENCY") == "") {
            $expiresAt = Carbon::now()->addYear(10);
            Cache::put('ZARINPAL_CURRENCY', env('ZARINPAL_CURRENCY', 'IRT'), $expiresAt);
        }

//        $this->configurePublishing();

    }

    protected function configurePublishing(){
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../stubs/abdal-zarinpal-pg.php' => config_path('abdal-zarinpal-pg.php'),
            ], 'abdal-zarinpal-pg-config');

        }
    }
}
