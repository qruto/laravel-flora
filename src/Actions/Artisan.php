<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Command;

class Artisan extends Action
{
    private $command;

    private $arguments;

    public function __construct(Command $artisanCommand, string $command, array $arguments = [])
    {
        parent::__construct($artisanCommand);

        $this->command = $command;
        $this->arguments = $arguments;
    }

    public function title(): string
    {
        return "<comment>Run artisan command:</comment> $this->command (".
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

            return $initializerCommand->call($this->command, $this->arguments) === 0;
        }

        return $initializerCommand->callSilent($this->command, $this->arguments) === 0;
    }
}
