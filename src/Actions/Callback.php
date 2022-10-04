<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Command;

class Callback extends Action
{
    public function __construct(
        Command $initializerCommand,
        protected $callback,
        protected array $parameters = []
    ) {
        parent::__construct($initializerCommand);
    }

    public function title(): string
    {
        is_callable($this->callback, callable_name: $name);

        return "<fg=yellow>Calling</> $name";
    }

    public function run(): bool
    {
        if ($this->getInitializerCommand()->getOutput()->isVerbose()) {
            $this->getInitializerCommand()->getOutput()->newLine();
        }

        $result = call_user_func($this->callback, ...$this->parameters);

        if (! is_bool($result) && $this->getInitializerCommand()->getOutput()->isVerbose()) {
            $this->getInitializerCommand()->line('<options=bold>Returned result:</>');
            $this->getInitializerCommand()->line(var_export($result, true));
        }

        return is_bool($result) ? $result : true;
    }
}
