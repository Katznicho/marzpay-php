<?php

namespace MarzPay\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use MarzPay\MarzPay;

class MarzPayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/marzpay.php', 'marzpay');

        $this->app->singleton(MarzPay::class, function ($app) {
            $config = $app['config']['marzpay'];
            
            return new MarzPay([
                'api_key' => $config['api_key'],
                'api_secret' => $config['api_secret'],
                'base_url' => $config['base_url'],
                'timeout' => $config['timeout'],
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/marzpay.php' => config_path('marzpay.php'),
            ], 'marzpay-config');
        }
    }
}

