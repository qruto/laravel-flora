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
use Qruto\Power\Run;
use Qruto\Power\UndefinedScriptException;

abstract class PowerCommand extends Command
{
    use PackageDiscover;

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
            $this->discoverPackages($this->type, $env, $run);
        }

        $this->components->alert('Application '.$this->title());

        $this->output->newLine();

        $run->internal->start();

        $assetsFailed = false;

        $this->output->newLine();

        if ($assetsVersion->outdated()) {
            $assetsFailed = ! $this->laravel[Assets::class]->publish($this->type, $this->components, $this->output->isVerbose());
        } else {
            $this->components->twoColumnDetail('<fg=green>No assets for publishing</>');
        }

        $assetsVersion->stampUpdate();

        $this->output->newLine();

        if ($run->internal->doneWithFailures() || $assetsFailed) {
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
}
