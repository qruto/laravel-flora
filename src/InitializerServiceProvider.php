<?php

namespace MadWeb\Initializer;

use Illuminate\Support\ServiceProvider;
use MadWeb\Initializer\Contracts\Runner;
use MadWeb\Initializer\Console\Commands\InstallCommand;
use MadWeb\Initializer\Console\Commands\InstallerMakeCommand;
use MadWeb\Initializer\Contracts\Executor as ExecutorContract;

class InitializerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/initializer.php' => config_path('initializer.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/initializer.php', 'initializer');

            $this->app->singleton('command.initializer.install', InstallCommand::class);
            $this->app->singleton('command.initializer.installer.make', InstallerMakeCommand::class);
            $this->app->bind('project.installer', \App\InstallerConfig::class);
            $this->app->bind(Runner::class, Run::class);
            $this->app->bind(ExecutorContract::class, Executor::class);

            $this->commands(['command.initializer.install', 'command.initializer.installer.make']);
        }
    }
}
