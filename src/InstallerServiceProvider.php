<?php

namespace MadWeb\Initializer;

use Illuminate\Support\ServiceProvider;
use MadWeb\Initializer\Contracts\Runner;
use MadWeb\Initializer\Console\Commands\InstallCommand;
use MadWeb\Initializer\Console\Commands\InstallerMakeCommand;
use MadWeb\Initializer\Contracts\Executor as ExecutorContract;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-installer.php' => config_path('laravel-installer.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->mergeConfigFrom(__DIR__.'/../config/laravel-installer.php', 'laravel-installer');

            $this->app->singleton('command.installer.install', InstallCommand::class);
            $this->app->singleton('command.installer.make', InstallerMakeCommand::class);
            $this->app->bind('project.installer', \App\InstallerConfig::class);
            $this->app->bind(Runner::class, Run::class);
            $this->app->bind(ExecutorContract::class, Executor::class);

            $this->commands(['command.installer.install', 'command.installer.make']);
        }
    }
}
