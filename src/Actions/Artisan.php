<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Command;

class Artisan extends Action
{
    public function __construct(
        Command $artisanCommand,
        protected string $command,
        protected array $parameters = []
    ) {
        parent::__construct($artisanCommand);
    }

    public function title(): string
    {
        return "<fg=yellow>Running</> $this->command (".
            $this->getInitializerCommand()
                ->getApplication()
                ->find($this->command)
                ->getDescription().
            ')';
    }

    public function run(): bool
    {
        $initializerCommand = $this->getInitializerCommand();

        if ($initializerCommand->getOutput()->isVerbose()) {
            $initializerCommand->getOutput()->newLine();

            return $initializerCommand->call($this->command, $this->parameters) === 0;
        }

        return $initializerCommand->callSilent($this->command, $this->parameters) === 0;
    }
}
