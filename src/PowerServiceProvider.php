<?php

namespace Qruto\Power;

use Illuminate\Foundation\Application;
use Qruto\Power\Console\Commands\InstallCommand;
use Qruto\Power\Console\Commands\SetupCommand;
use Qruto\Power\Console\Commands\UpdateCommand;
use Qruto\Power\Contracts\Chain as ChainContract;
use Qruto\Power\Contracts\ChainVault as ChainVaultContract;
use Qruto\Power\Discovers\HorizonDiscover;
use Qruto\Power\Discovers\IdeHelperDiscover;
use Qruto\Power\Discovers\VaporUiDiscover;
use Qruto\Power\Enums\PowerType;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PowerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('power')
            ->hasConfigFile()
            ->hasCommands(
                InstallCommand::class,
                UpdateCommand::class,
                SetupCommand::class
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
            fn (string $environment, callable $callback) => $vault->get(PowerType::Install)->set($environment, $callback)
        );

        Application::macro(
            'update',
            fn (string $environment, callable $callback) => $vault->get(PowerType::Update)->set($environment, $callback)
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

        $this->app->singleton('power.packages', fn () => [
            new VaporUiDiscover(),
            new HorizonDiscover(),
            new IdeHelperDiscover(),
        ]);
    }
}
