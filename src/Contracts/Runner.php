<?php

namespace ZFort\AppInstaller\Contracts;

interface Runner
{
    public function artisan(string $command, ...$arguments): self;

    public function external(string $command, ...$arguments): self;

    public function callable(callable $function, ...$arguments): self;

    public function getCommands(): array;
}
