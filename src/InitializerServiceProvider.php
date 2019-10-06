<?php

namespace MadWeb\Initializer;

use Illuminate\Support\ServiceProvider;
use MadWeb\Initializer\Console\Commands\InstallCommand;
use MadWeb\Initializer\Console\Commands\UpdateCommand;
use MadWeb\Initializer\Contracts\Runner;
use NunoMaduro\LaravelConsoleTask\LaravelConsoleTaskServiceProvider;

class InitializerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/initializer.php' => config_path('initializer.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../stubs/install-class.stub' => app_path('Install.php'),
            __DIR__.'/../stubs/update-class.stub' => app_path('Update.php'),
        ], 'initializers');

        $this->app->register(LaravelConsoleTaskServiceProvider::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                UpdateCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/initializer.php', 'initializer');

        $this->app->bind('app.installer', \App\Install::class);
        $this->app->bind('app.updater', \App\Update::class);

        $this->app->bind(Runner::class, Run::class);
    }
}
