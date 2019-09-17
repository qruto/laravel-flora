<?php

namespace MadWeb\Initializer;

use Illuminate\Support\ServiceProvider;
use MadWeb\Initializer\Contracts\Runner;
use MadWeb\Initializer\Console\Commands\UpdateCommand;
use MadWeb\Initializer\Console\Commands\InstallCommand;
use MadWeb\Initializer\Console\Commands\InitializersMakeCommand;
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

        $this->app->register(LaravelConsoleTaskServiceProvider::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/initializer.php', 'initializer');

        $this->app->singleton('command.initializer.install', InstallCommand::class);
        $this->app->singleton('command.initializer.update', UpdateCommand::class);
        $this->app->singleton('command.initializer.make', InitializersMakeCommand::class);

        $this->app->bind('project.installer', \App\Install::class);
        $this->app->bind('project.updater', \App\Update::class);

        $this->app->bind(Runner::class, Run::class);

        $this->commands([
            'command.initializer.install',
            'command.initializer.update',
            'command.initializer.make',
        ]);
    }
}
