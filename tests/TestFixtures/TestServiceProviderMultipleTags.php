<?php

namespace Qruto\Formula\Tests\TestFixtures;

use Illuminate\Support\ServiceProvider;

class TestServiceProviderMultipleTags extends ServiceProvider
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
        ], 'one');

        $this->publishes([
            __DIR__.'/asset-two.txt' => public_path('asset-two.txt'),
        ], 'two');
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
