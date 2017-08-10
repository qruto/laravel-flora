<?php

namespace ZFort\AppInstaller;

use Illuminate\Support\ServiceProvider;
use ZFort\AppInstaller\Contracts\Runner;
use ZFort\AppInstaller\Console\Commands\InstallCommand;
use ZFort\AppInstaller\Console\Commands\InstallerMakeCommand;
use ZFort\AppInstaller\Contracts\Executor as ExecutorContract;

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
