<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Application;

class Artisan extends Action
{
    protected static string $label = 'command';

    protected string $color = 'yellow';

    public function __construct(
        protected Application $application,
        protected string $command,
        protected array $parameters = [],
    ) {
    }

    public function name(): string
    {
        return $this->command;
    }

    public function description(): string
    {
        return $this->application->find($this->command)->getDescription();
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
