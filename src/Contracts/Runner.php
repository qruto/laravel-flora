<?php

namespace Qruto\Initializer\Contracts;

interface Runner
{
    public function command(string $command, array $parameters = []): self;

    public function exec(string $command, array $parameters = []): self;

    public function call(callable $callback, array $parameters = []): self;

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): self;
}
