<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Command;

class Callback extends Action
{
    private $function;

    private $arguments;

    public function __construct(Command $initializerCommand, callable $function, array $arguments = [])
    {
        parent::__construct($initializerCommand);

        $this->function = $function;
        $this->arguments = $arguments;
    }

    public function title(): string
    {
        is_callable($this->function, callable_name: $name);

        return '<comment>Call function:</comment> '.$name;
    }

    public function run(): bool
    {
        if ($this->getInitializerCommand()->getOutput()->isVerbose()) {
            $this->getInitializerCommand()->getOutput()->newLine();
        }

        $result = call_user_func($this->function, ...$this->arguments);

        if (! is_bool($result) && $this->getInitializerCommand()->getOutput()->isVerbose()) {
            $this->getInitializerCommand()->line('<options=bold>Returned result:</>');
            $this->getInitializerCommand()->line(var_export($result, true));
        }

        return is_bool($result) ? $result : true;
    }
}
