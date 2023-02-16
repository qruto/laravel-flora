<?php

namespace Qruto\Formula\Tests\TestFixtures;

use Illuminate\Support\ServiceProvider;

class TestServiceProviderOne extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/asset-one.txt' => public_path('asset-one.txt'),
        ], 'public');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
