<?php

namespace Qruto\Initializer;

use Illuminate\Foundation\Application;
use Qruto\Initializer\Console\Commands\InstallCommand;
use Qruto\Initializer\Console\Commands\PublishCommand;
use Qruto\Initializer\Console\Commands\UpdateCommand;
use Qruto\Initializer\Contracts\Chain as ChainContract;
use Qruto\Initializer\Contracts\ChainVault as ChainVaultContract;
use Qruto\Initializer\Enums\InitializerType;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class InitializerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('initializer')
            ->hasConfigFile()
            ->hasCommands(
                InstallCommand::class,
                UpdateCommand::class,
                PublishCommand::class
            );
    }

    /**
     * Bootstrap the application services.
     */
    public function packageBooted(): void
    {
        $vault = $this->app->make(ChainVaultContract::class);

        //TODO: refactor
        Application::macro(
            'install',
            fn (string $environment, callable $callback) => $vault->get(InitializerType::Install)->set($environment, $callback)
        );

        Application::macro(
            'update',
            fn (string $environment, callable $callback) => $vault->get(InitializerType::Update)->set($environment, $callback)
        );

        Run::newInstruction('build', fn (Run $run) => $run
            ->exec('npm install')
            ->exec('npm run build')
        );

        Run::newInstruction('cache', fn (Run $run) => $run
            ->command('route:cache')
            ->command('config:cache')
            ->command('event:cache')
        );
    }

    /**
     * Register the application services.
     */
    public function packageRegistered(): void
    {
        $this->app->bind(ChainContract::class, Chain::class);
        $this->app->singleton(ChainVaultContract::class, ChainVault::class);
    }
}
