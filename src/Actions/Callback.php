<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Contracts\Container\Container;

class Callback extends Action
{
    public function __construct(
        protected Container $container,
        protected $callback,
        protected array $parameters = [],
        protected ?string $name = null,
    ) {
    }

    public function title(): string
    {
        if ($this->name) {
            $name = $this->name;
        } else {
            is_callable($this->callback, callable_name: $name);
        }

        return "<fg=yellow>Calling</> $name";
    }

    public function run(): bool
    {
        $this->container->call($this->callback, $this->parameters);

        return true;
    }
}
