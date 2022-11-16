<?php

namespace Qruto\Initializer\Actions;

class Callback extends Action
{
    public function __construct(
        protected $callback,
        protected array $parameters = []
    ) {
    }

    public function title(): string
    {
        is_callable($this->callback, callable_name: $name);

        return "<fg=yellow>Calling</> $name";
    }

    public function run(): bool
    {
        $result = call_user_func($this->callback, ...$this->parameters);

        return is_bool($result) ? $result : true;
    }
}
