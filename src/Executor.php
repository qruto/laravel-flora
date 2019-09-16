<?php

namespace MadWeb\Initializer;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use MadWeb\Initializer\ExecutorActions\Artisan;
use MadWeb\Initializer\ExecutorActions\Callback;
use MadWeb\Initializer\ExecutorActions\Dispatch;
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
        value(new Callback($this->artisanCommand, $function, $arguments))();
    }

    public function dispatch($job)
    {
        value(new Dispatch($this->artisanCommand, $job))();
    }

    public function dispatchNow($job)
    {
        value(new Dispatch($this->artisanCommand, $job, true))();
    }
}
