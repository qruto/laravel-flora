<?php

namespace Qruto\Initializer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Events\VendorTagPublished;
use Qruto\Initializer\Actions\ActionTerminatedException;
use Qruto\Initializer\AssetsVersion;
use Qruto\Initializer\Contracts\Chain;
use Qruto\Initializer\Contracts\ChainVault;
use Qruto\Initializer\Enums\InitializerType;
use Qruto\Initializer\Run;
use Qruto\Initializer\UndefinedScriptException;
use function rtrim;

abstract class AbstractInitializeCommand extends Command
{
    use PackageDiscover;

    protected InitializerType $type;

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

        $initializer = $this->getInitializer($vault);

        $env = $this->getLaravel()->environment();

        $runner = $container->make(Run::class, [
            'application' => $this->getApplication(),
            'output' => $this->getOutput(),
        ]);

        $this->trap([SIGTERM, SIGINT], function ($signal) use ($runner) {
            if ($this->components->confirm('Installation stop confirm')) {
                $this->components->warn(ucfirst($this->title()).' aborted without completion');
                exit;
            }

            $runner->internal->rerunLatestAction();

            throw new ActionTerminatedException($runner->internal->getLatestAction(), $signal);
        });

        try {
            $container->call($initializer->get($env), ['run' => $runner]);
        } catch (UndefinedScriptException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($autoInstruction) {
            $this->discoverPackages($this->type, $env, $runner);
        }

        $this->components->alert('Application '.$this->title());

        $this->output->newLine();

        $runner->internal->start();

        if ($assetsVersion->outdated()) {
            $this->publishAssets(
                $config->get('initializer.assets'),
                $config->get('initializer.always_publish')
            );
        } else {
            $this->output->newLine();
            $this->components->twoColumnDetail('<fg=green>No assets for publishing</>');
        }

        $assetsVersion->stampUpdate();

        $this->output->newLine();

        if ($runner->internal->doneWithFailures()) {
            $exceptions = $runner->internal->exceptions();

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
     * Returns initializer instance for current command.
     */
    protected function getInitializer(ChainVault $vault): Chain
    {
        return $vault->get($this->type);
    }

    abstract protected function title(): string;

    private function publishAssets(array $assets, bool $alwaysPublish): void
    {
        if ($assets === []) {
            return;
        }

        if ($this->type === InitializerType::Install && ! $alwaysPublish) {
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
