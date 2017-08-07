<?php

namespace ZFort\AppInstaller;

use ZFort\AppInstaller\Run;
use Illuminate\Support\ServiceProvider;
use ZFort\AppInstaller\Contracts\Runner;
use ZFort\AppInstaller\Console\Commands\Install;

class InstallerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->app->singleton('command.installer.install', Install::class);
            $this->app->bind('project.installer', \App\InstallerConfig::class);
            $this->app->bind(Runner::class, Run::class);

            $this->commands('command.installer.install');
        }
    }
}
