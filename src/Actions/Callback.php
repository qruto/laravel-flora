<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Factory;

class Callback extends Action
{
    public function __construct(
        Factory $outputComponents,
        protected $callback,
        protected array $parameters = []
    ) {
        parent::__construct($outputComponents);
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
