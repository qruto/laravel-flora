<?php

namespace MadWeb\Initializer\Test\TestFixtures;

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
            __DIR__.'/test-publishable-one.txt' => public_path('test-publishable-one.txt'),
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
