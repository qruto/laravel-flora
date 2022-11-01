<?php

namespace Qruto\Initializer;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Qruto\Initializer\Console\Commands\InstallCommand;
use Qruto\Initializer\Console\Commands\UpdateCommand;
use Qruto\Initializer\Contracts\Runner;
use Qruto\Initializer\Contracts\Chain as ChainContract;
use Qruto\Initializer\Contracts\ChainVault as ChainVaultContract;

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

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                UpdateCommand::class,
            ]);
        }

        $vault = $this->app->make(ChainVaultContract::class);

        //TODO: refactor
        Application::macro(
            'install',
            fn (string $environment, callable $callback) => $vault->getInstall()->set($environment, $callback)
        );

        Application::macro(
            'update',
            fn (string $environment, callable $callback) => $vault->getUpdate()->set($environment, $callback)
        );
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/initializer.php', 'initializer');

        $this->app->bind(Runner::class, Run::class);

        $this->app->bind(ChainContract::class, Chain::class);
        $this->app->singleton(ChainVaultContract::class, ChainVault::class);
    }
}
