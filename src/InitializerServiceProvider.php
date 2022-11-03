<?php

namespace Qruto\Initializer;

use Illuminate\Foundation\Application;
use Qruto\Initializer\Console\Commands\InstallCommand;
use Qruto\Initializer\Console\Commands\PublishCommand;
use Qruto\Initializer\Console\Commands\UpdateCommand;
use Qruto\Initializer\Contracts\Chain as ChainContract;
use Qruto\Initializer\Contracts\ChainVault as ChainVaultContract;
use Qruto\Initializer\Contracts\Runner;
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
    public function bootingPackage(): void
    {
        $vault = $this->app->make(ChainVaultContract::class);

        //TODO: refactor
        Application::macro(
            'install',
            static fn (string $environment, callable $callback) => $vault->get(InitializerType::Install)->set($environment, $callback)
        );

        Application::macro(
            'update',
            static fn (string $environment, callable $callback) => $vault->get(InitializerType::Update)->set($environment, $callback)
        );
    }

    /**
     * Register the application services.
     */
    public function registeringPackage(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/initializer.php', 'initializer');

        $this->app->bind(Runner::class, Run::class);

        $this->app->bind(ChainContract::class, Chain::class);
        $this->app->singleton(ChainVaultContract::class, ChainVault::class);
    }
}
