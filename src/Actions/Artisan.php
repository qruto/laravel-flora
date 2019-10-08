<?php

namespace MadWeb\Initializer\Actions;

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
            $this->getArtisanCommnad()
                ->getApplication()
                ->find($this->command)
                ->getDescription().
            ')';
    }

    public function run(): bool
    {
        $artisanCommand = $this->getArtisanCommnad();

        if ($artisanCommand->getOutput()->isVerbose()) {
            $artisanCommand->getOutput()->newLine();

            return ! $artisanCommand->call($this->command, $this->arguments);
        }

        return ! $artisanCommand->callSilent($this->command, $this->arguments);
    }
}
