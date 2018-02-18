<?php

namespace MadWeb\Initializer\Contracts;

interface Runner
{
    public function artisan(string $command, array $arguments = []): self;

    public function external(string $command, ...$arguments): self;

    public function callable(callable $function, ...$arguments): self;

    public function dispatch($job): self;

    public function dispatchNow($job): self;

    public function publish($providers): self;

    public function getCommands(): array;
}
