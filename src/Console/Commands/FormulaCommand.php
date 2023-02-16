<?php

namespace Qruto\Formula\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Events\VendorTagPublished;
use Qruto\Formula\Actions\ActionTerminatedException;
use Qruto\Formula\AssetsVersion;
use Qruto\Formula\Contracts\Chain;
use Qruto\Formula\Contracts\ChainVault;
use Qruto\Formula\Enums\FormulaType;
use Qruto\Formula\Run;
use Qruto\Formula\UndefinedScriptException;
use function rtrim;

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

        if ($assetsVersion->outdated()) {
            $this->publishAssets(
                $config->get('formula.assets'),
                $config->get('formula.always_publish')
            );
        } else {
            $this->output->newLine();
            $this->components->twoColumnDetail('<fg=green>No assets for publishing</>');
        }

        $assetsVersion->stampUpdate();

        $this->output->newLine();

        if ($run->internal->doneWithFailures()) {
            $exceptions = $run->internal->exceptions();

            if (! empty($exceptions) && $this->components->confirm('Show errors?')) {
                //TODO: make scrollable
                foreach ($exceptions as $exception) {
                    $this->components->twoColumnDetail($exception['title'], '<fg=red;options=bold>FAIL</>');

                    $exceptionHandler->renderForConsole($this->getOutput(), $exception['e']);
                    $exceptionHandler->report($exception['e']);

                    $this->output->newLine();
                    $this->output->newLine();
                }
            }

            $this->components->error(ucfirst($this->title()).' occur errors. You could run command with <fg=cyan>-v</> flag to see more details');

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

    private function publishAssets(array $assets, bool $alwaysPublish): void
    {
        if ($assets === []) {
            return;
        }

        if ($this->type === FormulaType::Install && ! $alwaysPublish) {
            return;
        }

        foreach (self::packagesToDiscover() as $package) {
            if ($package->exists() && $tag = $package->instruction()->assetsTag) {
                $assets[] = $tag;
            }
        }

        $this->output->newLine();

        $assetsString = '';

        foreach ($assets as $key => $value) {
            if (is_string($key)) {
                $assetsString .= $key.': '.(is_array($value) ? implode(', ', $value) : $value);
            } else {
                $assetsString .= $value;
            }

            $assetsString .= ', ';
        }

        $assetsString = rtrim($assetsString, ', ');

        $this->laravel['events']->listen(function (VendorTagPublished $event) {
            foreach ($event->paths as $from => $to) {
                $type = null;

                if (is_file($from)) {
                    $type = 'file';
                } elseif (is_dir($from)) {
                    $type = 'directory';
                }

                $type ? $this->components->task(sprintf(
                    'Copying %s [%s] to [%s]',
                    $type,
                    realpath($from),
                    realpath($to),
                )) : $this->components->error("Can't locate path: <{$from}>");
            }
        });

        $tags = [];

        $publishCallback = null;

        foreach ($assets as $key => $value) {
            $parameters = ['--provider' => '', '--tag' => []];

            if (is_string($key)) {
                $parameters['--provider'] = $key;
                $parameters['--tag'] = is_string($value) ? [$value] : $value;
            } elseif (class_exists($value)) {
                $parameters['--provider'] = $value;
                unset($parameters['--tag']);
            } else {
                $tags[] = $value;
            }

            if (! empty($parameters['--provider'])) {
                $publishCallback = fn () => $this->callSilent('vendor:publish', $parameters + ['--force' => true, '--no-interaction' => true]) === 0;
            }
        }

        if ($tags !== []) {
            $publishCallback = fn () => $this->callSilent('vendor:publish', ['--tag' => $tags, '--force' => true]) === 0;
        }

        $this->components->task(
            '<fg=yellow>Publishing assets</>'
            .($this->output->isVerbose() ? ' <fg=gray>'.$assetsString.'</>' : ''),
            $publishCallback
        );
    }
}
