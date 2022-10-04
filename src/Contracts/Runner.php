<?php

namespace Qruto\Initializer\Contracts;

interface Runner
{
    //TODO: leave only actions
    public function exceptions(): array;

    public function doneWithErrors(): bool;

    public function command(string $command, array $arguments = []): self;

    public function process(string $command, ...$arguments): self;

    public function call(callable $function, ...$arguments): self;

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): self;

    public function publish($providers, bool $force = false): self;

    public function publishForce($providers): self;

    public function publishTag($tag, bool $force = false): self;

    public function publishTagForce($tag): self;
}
