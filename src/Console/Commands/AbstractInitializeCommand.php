<?php

namespace MadWeb\Initializer\Console\Commands;

use MadWeb\Initializer\Run;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use MadWeb\Initializer\Contracts\Runner as ExecutorContract;

abstract class AbstractInitializeCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Container $container)
    {
        /** @var ExecutorContract $Executor */
        $config = $container->make('config');
        $env = $config->get($config->get('initializer.env_config_key'));

        $this->alert($this->title().' started');

        $result = call_user_func(
            [
                $this->getInitializerInstance($container),
                $this->option('root') ? $env.'Root' : $env,
            ],
            $container->makeWith(Run::class, ['artisanCommand' => $this])
        );

        $this->output->newLine();
//
//        if ($isDoneWithFailures) {
//            $this->error($this->title().' done with errors');
//            $this->error('You could rerun command with -v flag for see more details');
//        } else {
//            $this->info($this->title().' done!');
//        }
    }

    /**
     * Returns initializer instance for current command.
     *
     * @return object
     */
    abstract protected function getInitializerInstance(Container $container);

    abstract protected function title(): string;
}
