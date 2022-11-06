<?php

namespace Qruto\Initializer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Events\VendorTagPublished;
use Qruto\Initializer\AssetsVersion;
use Qruto\Initializer\Contracts\Chain;
use Qruto\Initializer\Contracts\ChainVault;
use Qruto\Initializer\Contracts\Runner;
use Qruto\Initializer\Enums\InitializerType;
use Qruto\Initializer\UndefinedInstructionException;

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

        // TODO: respect env option
        $env = $config->get($config->get('initializer.env_config_key'));

        $runner = $container->make(Runner::class, [
            'application' => $this->getApplication(),
            'output' => $this->getOutput(),
        ]);

        $this->trap([SIGTERM, SIGINT], function () use ($runner) {
            if ($this->components->confirm('Installation stop confirm')) {
                $this->components->warn(ucfirst($this->title()).' aborted without completion');
                exit;
            }

            $runner->internal->runLatestAction();
        });

        try {
            $container->call($initializer->get($env), ['run' => $runner]);
        } catch (UndefinedInstructionException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($autoInstruction) {
            $this->packageDiscovers($this->type, $env, $runner);
        }

        $latestAssetsHash = cache()->get('assets_hash');

        $this->components->alert('Application '.$this->title());

        $this->output->newLine();

        $runner->internal->start();

        // TODO: root options

        if ($assetsVersion->outdated($latestAssetsHash)) {
            $this->publishAssets($config->get('initializer.assets'), $config->get('initializer.production_build'));
            $assetsVersion->stampUpdate();
        }

        $this->output->newLine();

        if ($runner->internal->doneWithErrors()) {
            $exceptions = $runner->internal->exceptions();

            if (! empty($exceptions) && $this->components->confirm('Show errors?')) {
                //TODO: make scrollable
                foreach ($exceptions as $exception) {
                    $this->components->twoColumnDetail($exception['title']);

                    $exceptionHandler->renderForConsole($this->getOutput(), $exception['e']);
                    $exceptionHandler->report($exception['e']);

                    $this->output->newLine();
                    $this->output->newLine();
                }
            }

            // TODO: log errors
            $this->components->error($this->title().' occur errors');

            $this->line('<fg=red>You could run command with <fg=cyan>-v</> flag to see more details</>');

            return self::FAILURE;
        }

        $this->components->info($this->title().' done!');

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

    private function publishAssets(array $assets, bool $buildInProduction): void
    {
        if (empty($assets)) {
            return;
        }

        if ($this->type === InitializerType::Install && !$buildInProduction) {
            return;
        }

        $this->output->newLine();

        $this->components->twoColumnDetail('<fg=yellow>Publishing assets</> <fg=gray>' . implode(', ', $assets) . '</>');

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

        $parameters = [];

        foreach ($assets as $key => $value) {
            if (is_string($key)) {
                $parameters = ['--provider' => $key, '--tag' => $value];
            } else {
                if (class_exists($value)) {
                    $parameters = ['--provider' => $value];
                } else {
                    $parameters = ['--tag' => $value];
                }
            }
        }

        $this->callSilent('vendor:publish', $parameters + ['--force' => true]);
    }
}
