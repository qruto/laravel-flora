<?php

namespace Qruto\Initializer\Contracts;

interface Runner
{
    //TODO: leave only actions
    public function exceptions(): array;

    public function doneWithErrors(): bool;

    public function command(string $command, array $parameters = []): self;

    public function exec(string $command, array $parameters = []): self;

    public function call(callable $callback, array $parameters = []): self;

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): self;

    public function publish($providers, bool $force = false): self;

    public function publishForce($providers): self;

    public function publishTag($tag, bool $force = false): self;

    public function publishTagForce($tag): self;
}
