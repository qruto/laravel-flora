<?php

namespace Qruto\Power\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Qruto\Power\Actions\ActionTerminatedException;
use Qruto\Power\AssetsVersion;
use Qruto\Power\Console\Assets;
use Qruto\Power\Contracts\Chain;
use Qruto\Power\Contracts\ChainVault;
use Qruto\Power\Enums\PowerType;
use Qruto\Power\PackageDiscoverException;
use Qruto\Power\Run;
use Qruto\Power\UndefinedScriptException;

abstract class PowerCommand extends Command
{
    use PackageInstruction;

    /**
     * The type of build.
     */
    protected PowerType $type;

    /**
     * Execute the action.
     */
    public function handle(
        Container $container,
        AssetsVersion $assetsVersion,
        ChainVault $vault,
        ExceptionHandler $exceptionHandler
    ): int {
        $autoInstruction = $this->loadInstructions();

        $power = $this->getPower($vault);

        $env = $this->getLaravel()->environment();

        $run = $container->make(Run::class, [
            'application' => $this->getApplication(),
            'output' => $this->getOutput(),
        ]);

        $this->trap([SIGTERM, SIGINT], function ($signal) use ($run) {
            if ($this->components->confirm('Installation stop confirm')) {
                $this->components->warn(ucfirst($this->title()).' aborted without completion');
                exit;
            }

            $run->internal->rerunLatestAction();

            throw new ActionTerminatedException($run->internal->getLatestAction(), $signal);
        });

        try {
            $container->call($power->get($env), ['run' => $run]);
        } catch (UndefinedScriptException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($autoInstruction) {
            $this->instructPackages($this->type, $env, $run);
        }

        $this->components->alert(sprintf('Application %s', $this->title()));

        $this->output->newLine();

        $packagesDiscovered = $this->discoverPackages();

        $assetsPublished = $this->publishAssets($assetsVersion);

        if ($this->output->isVerbose()) {
            $this->components->info('Running actions');
        } else {
            $this->output->newLine();
        }

        $run->internal->start();

        $this->output->newLine();

        $assetsVersion->stampUpdate();

        if ($run->internal->doneWithFailures() || ! $assetsPublished || ! $packagesDiscovered) {
            $this->askToShowErrors($run->internal->exceptions(), $exceptionHandler);

            $this->components->error(ucfirst($this->title()).' occur errors. Run with <fg=cyan>-v</> flag to see more details');

            return self::FAILURE;
        }

        $this->components->info(ucfirst($this->title()).' done!');

        return self::SUCCESS;
    }

    /**
     * Returns vault of instructions for current command type.
     */
    protected function getPower(ChainVault $vault): Chain
    {
        return $vault->get($this->type);
    }

    /**
     * Returns command action title.
     */
    abstract protected function title(): string;

    /**
     * Ask user to show errors.
     */
    private function askToShowErrors(array $exceptions, ExceptionHandler $exceptionHandler): void
    {
        if ($exceptions === []) {
            return;
        }

        if (! $this->components->confirm('Show errors?')) {
            return;
        }

        foreach ($exceptions as $exception) {
            $this->components->twoColumnDetail($exception['title'], '<fg=red;options=bold>FAIL</>');

            $exceptionHandler->renderForConsole($this->getOutput(), $exception['e']);
            $exceptionHandler->report($exception['e']);

            $this->output->newLine();
            $this->output->newLine();
        }
    }

    /**
     * Load custom build instructions.
     */
    private function loadInstructions(): bool
    {
        $autoInstruction = true;

        if ($customBuildExists = file_exists($build = base_path('routes/setup.php'))) {
            $autoInstruction = false;

            require $build;
        } else {
            require __DIR__.'/../../setup.php';
        }

        return $autoInstruction;
    }

    private function discoverPackages(): bool
    {
        if ($this->output->isVerbose()) {
            return $this->call('package:discover') === 0;
        }

        try {
            /** @throws PackageDiscoverException */
            $this->components->task(
                'Packages discovery',
                function () {
                    if ($this->callSilent('package:discover') !== 0) {
                        throw new PackageDiscoverException();
                    }
                }
            );

            return true;
        } catch (PackageDiscoverException) {
            return false;
        }
    }

    private function publishAssets(AssetsVersion $assetsVersion): bool
    {
        $success = true;

        if ($assetsVersion->outdated()) {
            $success = $this->laravel[Assets::class]->publish($this->components, $this->output->isVerbose());
        } else {
            $this->components->twoColumnDetail('<fg=green>No assets for publishing</>');
        }

        if ($this->output->isVerbose()) {
            $this->output->newLine();
        }

        return $success;
    }
}
