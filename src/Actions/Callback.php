<?php

namespace MadWeb\Initializer\Actions;

use Illuminate\Console\Command;

class Callback extends Action
{
    private $function;

    private $arguments;

    public function __construct(Command $artisanCommand, callable $function, array $arguments = [])
    {
        parent::__construct($artisanCommand);

        $this->function = $function;
        $this->arguments = $arguments;
    }

    public function title(): string
    {
        is_callable($this->function, false, $name);

        return '<comment>Calling function:</comment> '.$name;
    }

    public function message(): string
    {
        return '';
    }

    public function run(): bool
    {
        if ($this->getArtisanCommnad()->getOutput()->isVerbose()) {
            $this->getArtisanCommnad()->getOutput()->newLine();
        }

        return call_user_func($this->function, ...$this->arguments);
    }
}
