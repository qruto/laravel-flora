<?php

namespace Qruto\Initializer\Console\Commands;

use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Qruto\Initializer\Contracts\Chain;
use Qruto\Initializer\Contracts\ChainVault;
use Qruto\Initializer\Run;

abstract class AbstractInitializeCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Container $container, Repository $config, ChainVault $vault, ExceptionHandler $exceptionHandler)
    {
        require base_path('routes/build.php');

        $initializer = $this->getInitializer($vault);

        // TODO: respect env option
        $env = $config->get($config->get('initializer.env_config_key'));

        $this->components->alert('Application ' . $this->title());

        $runner = $container->make(Run::class, ['initializerCommand' => $this]);

        $container->call($initializer->get($env), ['run' => $runner]);

        // TODO: root options

        $this->output->newLine();

        if ($runner->doneWithErrors()) {
            $exceptions = $runner->exceptions();

            $this->components->error($this->title(). ' occur errors');

            if (! empty($exceptions)) {
                $this->output->newLine();

                foreach ($exceptions as $exception) {
                    $exceptionHandler->renderForConsole($this->getOutput(), $exception);
                    $this->output->newLine();
                }
            }

            $this->line('<fg=red>You could run command with <fg=cyan>-v</> flag to see more details</>');

            return 1;
        }

        $this->components->info($this->title().' done!');

        return 0;
    }

    /**
     * Returns initializer instance for current command.
     *
     * @return object
     */
    abstract protected function getInitializer(ChainVault $vault): Chain;

    abstract protected function title(): string;
}
