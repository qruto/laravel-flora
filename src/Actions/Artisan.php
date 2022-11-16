<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Application;

class Artisan extends Action
{
    public function __construct(
        protected Application $application,
        protected string $command,
        protected array $parameters = []
    ) {
    }

    public function title(): string
    {
        return "<fg=yellow>Running</> $this->command <fg=gray>(".
            $this->application->find($this->command)->getDescription().
        ')</>';
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function run(): bool
    {
        return $this->application->call($this->command, $this->parameters + ['--no-interaction' => true]) === 0;
    }
}
