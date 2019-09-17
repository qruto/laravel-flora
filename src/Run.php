<?php

namespace MadWeb\Initializer;

use Illuminate\Console\Command;
use MadWeb\Initializer\ExecutorActions\Artisan;
use MadWeb\Initializer\ExecutorActions\Publish;
use MadWeb\Initializer\ExecutorActions\Callback;
use MadWeb\Initializer\ExecutorActions\Dispatch;
use MadWeb\Initializer\ExecutorActions\External;
use MadWeb\Initializer\Contracts\Runner as RunnerContract;

class Run implements RunnerContract
{
    protected $artisanCommand;

    public function __construct(Command $artisanCommand)
    {
        $this->artisanCommand = $artisanCommand;
    }

    public function artisan(string $command, array $arguments = []): RunnerContract
    {
        value(new Artisan($this->artisanCommand, $command, $arguments))();

        return $this;
    }

    public function publish($providers, bool $force = false): RunnerContract
    {
        value(new Publish($this->artisanCommand, $providers, $force))();

        return $this;
    }

    public function publishForce($providers): RunnerContract
    {
        return $this->publish($providers, true);
    }

    public function external(string $command, ...$arguments): RunnerContract
    {
        value(new External($this->artisanCommand, $command, $arguments))();

        return $this;
    }

    public function callable(callable $function, ...$arguments): RunnerContract
    {
        value(new Callback($this->artisanCommand, $function, $arguments))();

        return $this;
    }

    public function dispatch($job): RunnerContract
    {
        value(new Dispatch($this->artisanCommand, $job))();

        return $this;
    }

    public function dispatchNow($job): RunnerContract
    {
        value(new Dispatch($this->artisanCommand, $job, true))();

        return $this;
    }
}
