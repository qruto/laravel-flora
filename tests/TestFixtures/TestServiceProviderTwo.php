<?php

namespace MadWeb\Initializer\Test\TestFixtures;

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
            __DIR__.'/test-publishable-two.txt' => public_path('test-publishable-two.txt'),
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
