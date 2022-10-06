<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Factory;

class Artisan extends Action
{
    public function __construct(
        Factory $outputComponents,
        protected Application $application,
        protected string $command,
        protected array $parameters = []
    ) {
        parent::__construct($outputComponents);
    }

    public function title(): string
    {
        return "<fg=yellow>Running</> $this->command (".
            $this->application->find($this->command)->getDescription().
        ')';
    }

    public function run(): bool
    {
        return $this->application->call($this->command, $this->parameters) === 0;
    }
}
