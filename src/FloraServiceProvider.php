<?php

namespace Qruto\Flora;

use Illuminate\Foundation\Application;
use Qruto\Flora\Console\Commands\InstallCommand;
use Qruto\Flora\Console\Commands\SetupCommand;
use Qruto\Flora\Console\Commands\UpdateCommand;
use Qruto\Flora\Contracts\Chain as ChainContract;
use Qruto\Flora\Contracts\ChainVault as ChainVaultContract;
use Qruto\Flora\Discovers\HorizonDiscover;
use Qruto\Flora\Discovers\IdeHelperDiscover;
use Qruto\Flora\Discovers\TypeScriptTransformerDiscover;
use Qruto\Flora\Discovers\VaporUiDiscover;
use Qruto\Flora\Enums\FloraType;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FloraServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('flora')
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
        $this->createAppMacro();

        Run::newScript('build', fn (Run $run): Run => $run
            ->exec('npm ci')
            ->exec('npm run build')
        );

        Run::newScript('cache', fn (Run $run): Run => $run
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

        $this->app->singleton('flora.packages', fn (): array => [
            new VaporUiDiscover(),
            new HorizonDiscover(),
            new IdeHelperDiscover(),
            new TypeScriptTransformerDiscover(),
        ]);
    }

    private function createAppMacro(): void
    {
        $macros = [
            'install' => FloraType::Install,
            'update' => FloraType::Update,
        ];

        foreach ($macros as $macro => $type) {
            Application::macro(
                $macro,
                fn (string $environment, callable $callback) => $this->app->make(ChainVaultContract::class)->get($type)->set($environment, $callback)
            );
        }
    }
}
