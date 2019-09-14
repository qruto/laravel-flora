<?php

namespace MadWeb\Initializer;

use MadWeb\Initializer\Actions\Publish;
use MadWeb\Initializer\Contracts\Runner;

class Run implements Runner
{
    protected $commands = [];

    public function artisan(string $command, array $arguments = []): Runner
    {
        $this->pushCommand(__FUNCTION__, $command, $arguments);

        return $this;
    }

    public function external(string $command, ...$arguments): Runner
    {
        $this->pushCommand(__FUNCTION__, $command, $arguments);

        return $this;
    }

    public function callable(callable $function, ...$arguments): Runner
    {
        $this->pushCommand(__FUNCTION__, $function, $arguments);

        return $this;
    }

    public function dispatch($job): Runner
    {
        $this->pushCommand(__FUNCTION__, $job);

        return $this;
    }

    public function dispatchNow($job): Runner
    {
        $this->pushCommand(__FUNCTION__, $job);

        return $this;
    }

    public function publish($providers, bool $force = false): Runner
    {
        $publishAction = new Publish($providers, $force);
        $publishAction->handle();

        foreach ($publishAction->getArguments() as $argument) {
            $this->artisan(Publish::COMMAND, $argument);
        }

        return $this;
    }

    public function publishForce($providers): Runner
    {
        return $this->publish($providers, true);
    }

    protected function pushCommand(string $type, $command, array $arguments = [])
    {
        $this->commands[] = compact('type', 'command', 'arguments');
    }

    public function getCommands(): array
    {
        return $this->commands;
    }
}
