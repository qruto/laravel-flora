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

    private $isDoneWithErrors = false;

    public function __construct(Command $artisanCommand)
    {
        $this->artisanCommand = $artisanCommand;
    }

    public function exec(array $commands)
    {
        foreach ($commands as $command) {
            $isDone = $this->{$command['type']}($command['command'], $command['arguments']);

            if (! $this->isDoneWithErrors and $isDone === false) {
                $this->isDoneWithErrors = true;
            }
        }

        return $this->isDoneWithErrors;
    }

    public function isDoneWithErrors(): bool
    {
        return $this->isDoneWithErrors;
    }

    public function artisan(string $command, array $arguments = []): bool
    {
        return value(new Artisan($this->artisanCommand, $command, $arguments))();
    }

    public function external(string $command, array $arguments = []): bool
    {
        return value(new External($this->artisanCommand, $command, $arguments))();
    }

    public function callable(callable $function, array $arguments = []): bool
    {
        return value(new Callback($this->artisanCommand, $function, $arguments))();
    }

    public function dispatch($job): bool
    {
        return value(new Dispatch($this->artisanCommand, $job))();
    }

    public function dispatchNow($job): bool
    {
        return value(new Dispatch($this->artisanCommand, $job, true))();
    }
}
