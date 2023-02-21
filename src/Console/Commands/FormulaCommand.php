<?php

namespace Qruto\Formula\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Qruto\Formula\Actions\ActionTerminatedException;
use Qruto\Formula\AssetsVersion;
use Qruto\Formula\Console\Assets;
use Qruto\Formula\Contracts\Chain;
use Qruto\Formula\Contracts\ChainVault;
use Qruto\Formula\Enums\FormulaType;
use Qruto\Formula\Run;
use Qruto\Formula\UndefinedScriptException;

abstract class FormulaCommand extends Command
{
    use PackageDiscover;

    protected FormulaType $type;

    public function handle(
        Container $container,
        AssetsVersion $assetsVersion,
        Repository $config,
        ChainVault $vault,
        ExceptionHandler $exceptionHandler
    ): int {
        $autoInstruction = true;

        if ($customBuildExists = file_exists($build = base_path('routes/build.php'))) {
            $autoInstruction = false;

            require $build;
        } else {
            require __DIR__.'/../../build.php';
        }

        $formula = $this->getFormula($vault);

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
            $container->call($formula->get($env), ['run' => $run]);
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

        if ($assetsVersion->outdated()) {
            $this->output->newLine();

            $assetsFailed = ! $this->laravel[Assets::class]->publish($this->type, $this->components, $this->output->isVerbose());
        } else {
            $this->output->newLine();
            $this->components->twoColumnDetail('<fg=green>No assets for publishing</>');
        }

        $assetsVersion->stampUpdate();

        $this->output->newLine();

        if ($run->internal->doneWithFailures() || $assetsFailed) {
            $exceptions = $run->internal->exceptions();

            $this->askToShowErrors($exceptions, $exceptionHandler);

            $this->components->error(ucfirst($this->title()).' occur errors. Run with <fg=cyan>-v</> flag to see more details');

            return self::FAILURE;
        }

        $this->components->info(ucfirst($this->title()).' done!');

        return self::SUCCESS;
    }

    /**
     * Returns formula instance for current command.
     */
    protected function getFormula(ChainVault $vault): Chain
    {
        return $vault->get($this->type);
    }

    abstract protected function title(): string;

    private function askToShowErrors(array $exceptions, ExceptionHandler $exceptionHandler): void
    {
        if (! empty($exceptions) && $this->components->confirm('Show errors?')) {
            foreach ($exceptions as $exception) {
                $this->components->twoColumnDetail($exception['title'], '<fg=red;options=bold>FAIL</>');

                $exceptionHandler->renderForConsole($this->getOutput(), $exception['e']);
                $exceptionHandler->report($exception['e']);

                $this->output->newLine();
                $this->output->newLine();
            }
        }
    }
}
