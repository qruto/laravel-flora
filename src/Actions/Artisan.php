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
                $this->getArtisanCommnad()
                    ->getApplication()
                    ->find($this->command)
                    ->getDescription().
                ')';
        }

        return $title;
    }

    public function message(): string
    {
        return '';
    }

    public function run(): bool
    {
        $artisanCommnad = $this->getArtisanCommnad();

        if ($artisanCommnad->getOutput()->isVerbose()) {
            $artisanCommnad->getOutput()->newLine();

            return ! $artisanCommnad->call($this->command, $this->arguments);
        }

        return ! $artisanCommnad->callSilent($this->command, $this->arguments);
    }
}
