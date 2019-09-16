<?php

namespace MadWeb\Initializer;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use MadWeb\Initializer\ExecutorActions\Artisan;
use MadWeb\Initializer\ExecutorActions\External;
use MadWeb\Initializer\Contracts\Executor as ExecutorContract;

class Executor implements ExecutorContract
{
    protected $artisanCommand;

    public function __construct(Command $artisanCommand)
    {
        $this->artisanCommand = $artisanCommand;
    }

    public function exec(array $commands)
    {
        foreach ($commands as $command) {
            $this->{$command['type']}($command['command'], $command['arguments']);
        }
    }

    public function artisan(string $command, array $arguments = [])
    {
        value(new Artisan($this->artisanCommand, $command, $arguments))();
    }

    public function external(string $command, array $arguments = [])
    {
        value(new External($this->artisanCommand, $command, $arguments))();
    }

    public function callable(callable $function, array $arguments = [])
    {
        call_user_func($function, ...$arguments);

        is_callable($function, false, $name);
        $this->artisanCommand->info('Calling <fg=green;options=bold>callable</> "'.$name.'"...');
    }

    public function dispatch($job)
    {
        $this->printJob($job, Container::getInstance()->make(Dispatcher::class)->dispatch($job));
    }

    public function dispatchNow($job)
    {
        $this->printJob($job, Container::getInstance()->make(Dispatcher::class)->dispatchNow($job));
    }

    protected function printJob($job, $result)
    {
        $message = 'Dispatching <fg=green;options=bold>job</> "'.get_class($job).'"...';

        $message .= is_string($result) ? '. Result: '.$result : '';

        $this->artisanCommand->info($message);
    }
}
