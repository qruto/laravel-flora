<?php

namespace ZFort\AppInstaller\Contracts;

interface Runner
{
    public function artisan(string $command, array $arguments = []): self;

    public function external(string $command, array $arguments = []): self;

    public function callable(callable $function, array $arguments = []): self;

    public function getCommands(): array;
}
