<?php

namespace Qruto\Formula;

use Illuminate\Foundation\Application;
use Qruto\Formula\Console\Commands\InstallCommand;
use Qruto\Formula\Console\Commands\PublishCommand;
use Qruto\Formula\Console\Commands\UpdateCommand;
use Qruto\Formula\Contracts\Chain as ChainContract;
use Qruto\Formula\Contracts\ChainVault as ChainVaultContract;
use Qruto\Formula\Enums\FormulaType;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FormulaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('formula')
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
            fn (string $environment, callable $callback) => $vault->get(FormulaType::Install)->set($environment, $callback)
        );

        Application::macro(
            'update',
            fn (string $environment, callable $callback) => $vault->get(FormulaType::Update)->set($environment, $callback)
        );

        Run::newScript('build', fn (Run $run) => $run
            ->exec('npm install')
            ->exec('npm run build')
        );

        Run::newScript('cache', fn (Run $run) => $run
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
