<?php

namespace MadWeb\Initializer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use MadWeb\Initializer\Contracts\Executor as ExecutorContract;

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
        $Executor = $container->makeWith(ExecutorContract::class, ['artisanCommand' => $this]);

        $Config = $container->make('config');
        $env = $Config->get($Config->get('initializer.env_config_key'));

        $Executor->exec($container->call([
            $this->getInitializerInstance($container),
            $this->option('root') ? $env.'Root' : $env,
        ])->getCommands());
    }

    /**
     * Returns initializer instance for current command.
     *
     * @return object
     */
    abstract protected function getInitializerInstance(Container $container);
}
