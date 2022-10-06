<?php

namespace Qruto\Initializer\Console\Commands;

use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Qruto\Initializer\Contracts\Chain;
use Qruto\Initializer\Contracts\ChainVault;
use Qruto\Initializer\Contracts\Runner;
use Qruto\Initializer\Run;

abstract class AbstractInitializeCommand extends Command
{

    public function handle(Container $container, Repository $config, ChainVault $vault, ExceptionHandler $exceptionHandler): void
    {
        require base_path('routes/build.php');

        $initializer = $this->getInitializer($vault);

        // TODO: respect env option
        $env = $config->get($config->get('initializer.env_config_key'));

        $this->components->alert('Application ' . $this->title());

        $runner = $container->make(Runner::class, [
            'application' => $this->getApplication(),
            'output' => $this->getOutput(),
        ]);

        $this->trap([SIGTERM, SIGINT], function () use ($runner) {
            if ($this->components->confirm('Installation stop confirm')) {
                $this->components->warn(ucfirst($this->title()) . ' aborted without completion');
                exit;
            }

            $runner->runLatestAction();
        });

        $container->call($initializer->get($env), ['run' => $runner]);

        // TODO: root options

        $this->output->newLine();

        if ($runner->doneWithErrors()) {
            $exceptions = $runner->exceptions();

            $this->components->error($this->title(). ' occur errors');

            if (! empty($exceptions) && $this->components->confirm('Show errors?')) {
                foreach ($exceptions as $exception) {
                    $this->components->twoColumnDetail($exception['title']);

                    $exceptionHandler->renderForConsole($this->getOutput(), $exception['e']);
                    $exceptionHandler->report($exception['e']);

                    $this->output->newLine();
                    $this->output->newLine();
                }
            }

            $this->line('<fg=red>You could run command with <fg=cyan>-v</> flag to see more details</>');
        }

        $this->components->info($this->title().' done!');
    }

    /**
     * Returns initializer instance for current command.
     *
     * @return object
     */
    abstract protected function getInitializer(ChainVault $vault): Chain;

    abstract protected function title(): string;
}
