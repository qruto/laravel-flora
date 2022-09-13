<?php

namespace Qruto\Initializer;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Qruto\Initializer\Console\Commands\InstallCommand;
use Qruto\Initializer\Console\Commands\UpdateCommand;
use Qruto\Initializer\Contracts\Runner;
use NunoMaduro\LaravelConsoleTask\LaravelConsoleTaskServiceProvider;
use Qruto\Initializer\Builder;
use Qruto\Initializer\Chain;
use Qruto\Initializer\Contracts\BuilderContract;
use Qruto\Initializer\Contracts\ChainContract;
use Qruto\Initializer\Contracts\ChainStoreContract;

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

        $builder = $this->app->make(BuilderContract::class);

        Application::macro('install', fn () => $builder->install());
        Application::macro('update', fn () => $builder->update());
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/initializer.php', 'initializer');

        $this->app->bind(Runner::class, Run::class);

        $this->app->singleton(BuilderContract::class, Builder::class);
        $this->app->singleton(ChainStoreContract::class, ChainStore::class);
        $this->app->singleton(ChainContract::class, Chain::class);
    }
}
