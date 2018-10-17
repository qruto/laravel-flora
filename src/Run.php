<?php

namespace MadWeb\Initializer;

use InvalidArgumentException;
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
        if (is_string($providers)) {
            $arguments = ['--provider' => $providers];

            if ($force) {
                $arguments['--force'] = true;
            }

            $this->artisan('vendor:publish', $arguments);
        } elseif (is_array($providers)) {
            foreach ($providers as $provider => $tag) {
                $arguments = [];

                $arguments['--provider'] = is_numeric($provider) ? $tag : $provider;

                if (! is_numeric($provider) and is_string($tag)) {
                    $arguments['--tag'] = $tag;
                }

                if ($force) {
                    $arguments['--force'] = true;
                }

                $this->artisan('vendor:publish', $arguments);
            }
        } else {
            throw new InvalidArgumentException('Invalid publishable argument.');
        }

        return $this;
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
