<?php

namespace Qruto\Formula\Tests\TestFixtures;

use Illuminate\Support\ServiceProvider;

class TestServiceProviderTwo extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/asset-two.txt' => public_path('asset-two.txt'),
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
