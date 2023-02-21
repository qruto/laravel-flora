<?php

namespace Qruto\Formula\Actions;

use Illuminate\Console\Application;

class Artisan extends Action
{
    /** Label for artisan command */
    public static string $label = 'command';

    /** Show artisan command label in yellow color */
    protected string $color = 'yellow';

    public function __construct(
        protected Application $application,
        protected string $command,
        protected array $parameters = [],
    ) {
    }

    /** Get artisan command name */
    public function name(): string
    {
        return $this->command;
    }

    /** Get artisan command description */
    public function description(): string
    {
        if (! $this->output->isVerbose()) {
            return '';
        }

        return $this->application->find($this->command)->getDescription();
    }

    /** Get artisan command parameters */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /** Call artisan command in no interaction mode */
    public function run(): bool
    {
        return $this->application->call($this->command, $this->parameters + ['--no-interaction' => true]) === 0;
    }
}
