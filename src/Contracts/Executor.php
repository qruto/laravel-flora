<?php

namespace MadWeb\Initializer\Contracts;

use Illuminate\Console\Command;

interface Executor
{
    public function __construct(Command $artisanCommand);

    public function exec(array $commands);

    public function artisan(string $command, array $arguments = []): bool;

    public function external(string $command, array $arguments = []): bool;

    public function callable(callable $function, array $arguments = []): bool;

    public function dispatch($job): bool;

    public function dispatchNow($job): bool;
}
