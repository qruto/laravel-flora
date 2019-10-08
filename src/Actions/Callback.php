<?php

namespace MadWeb\Initializer\Actions;

use Illuminate\Console\Command;

class Callback extends Action
{
    protected const LOADING_TEXT = 'calling';

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

        return '<comment>Call function:</comment> '.$name;
    }

    public function run(): bool
    {
        if ($this->getArtisanCommnad()->getOutput()->isVerbose()) {
            $this->getArtisanCommnad()->getOutput()->newLine();
        }

        $result = call_user_func($this->function, ...$this->arguments);

        if (! is_bool($result) && $this->getArtisanCommnad()->getOutput()->isVerbose()) {
            $this->getArtisanCommnad()->line('<options=bold>Returned result:</>');
            $returnResult = var_export($result, true);
            $this->getArtisanCommnad()->line($returnResult);
        }

        return is_bool($result) ? $result : true;
    }
}
