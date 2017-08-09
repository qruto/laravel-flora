<?php

namespace ZFort\AppInstaller;

use ZFort\AppInstaller\Contracts\Runner;

class Run implements Runner
{
    protected $commands = [];

    public function artisan(string $command, ...$arguments): Runner
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

    protected function pushCommand(string $type, $command, array $arguments)
    {
        foreach ($arguments as $index => $argument) {
            if (is_array($argument)) {
                $key = array_keys($argument)[0];
                $value = array_values($argument)[0];

                unset($arguments[$index]);
                $arguments[$key] = $value;
            }
        }
        $this->commands[] = compact('type', 'command', 'arguments');
    }

    public function getCommands(): array
    {
        return $this->commands;
    }
}
