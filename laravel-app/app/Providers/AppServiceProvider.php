<?php

namespace App\Providers;

use App\Services\Payment\VNPayService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(VNPayService::class, function ($app) {
            return new VNPayService(
                config('vnpay.tmn_code'),
                config('vnpay.hash_secret'),
                config('vnpay.url'),
                config('vnpay.return_url') ?: route('payment.vnpay.return'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
