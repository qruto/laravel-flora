<?php

namespace MadWeb\Initializer\Contracts;

use Illuminate\Console\Command;

interface Executor
{
    public function __construct(Command $artisanCommand);

    public function exec(array $commands);

    public function artisan(string $command, array $arguments = []);

    public function external(string $command, array $arguments = []);

    public function callable(callable $function, array $arguments = []);

    public function dispatch($job);

    public function dispatchNow($job);
}
