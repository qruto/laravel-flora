<?php

namespace MadWeb\Initializer\ExecutorActions;

use Illuminate\Console\Command;

class Artisan
{
    private $artisanCommand;

    private $command;

    private $arguments;

    public function __construct(Command $artisanCommand, string $command, array $arguments = [])
    {
        $this->artisanCommand = $artisanCommand;
        $this->command = $command;
        $this->arguments = $arguments;
    }

    private function title()
    {
        $title = '';

        if ($this->command === 'vendor:publish') {
            $title = '<comment>Publishing resource:</comment> ';

            if (isset($this->arguments['--provider'])) {
                $title .= "Provider [<fg=cyan>{$this->arguments['--provider']}</>]";
            }

            $tagStringCallback = function (string $tag) {
                return " Tag[<fg=cyan>$tag</>]";
            };

            if (isset($this->arguments['--tag'])) {
                if (is_string($this->arguments['--tag'])) {
                    $title .= $tagStringCallback($this->arguments['--tag']);
                } else {
                    foreach ($this->arguments['--tag'] as $tag) {
                        $title .= $tagStringCallback($tag);
                    }
                }
            }
        } else {
            $title = "<comment>Running artisan command:</comment> $this->command (".
                $this->artisanCommand
                    ->getApplication()
                    ->find($this->command)
                    ->getDescription().
                ')';
        }

        return $title;
    }

    public function __invoke(): bool
    {
        return $this->artisanCommand->task($this->title(), function () {
            if ($this->artisanCommand->getOutput()->isVerbose()) {
                $this->artisanCommand->getOutput()->newLine();

                return ! $this->artisanCommand->call($this->command, $this->arguments);
            }

            return ! $this->artisanCommand->callSilent($this->command, $this->arguments);
        });
    }
}
