<?php

namespace Qruto\Formula\Actions;

use Illuminate\Contracts\Container\Container;

class Callback extends Action
{
    public static string $label = 'call';

    protected string $color = 'cyan';

    public function __construct(
        protected Container $container,
        protected $callback,
        protected array $parameters = [],
        protected ?string $name = null,
    ) {
    }

    public function name(): string
    {
        $name = '';

        if ($this->name) {
            $name = $this->name;
        } else {
            is_callable($this->callback, callable_name: $name);
        }

        return $name;
    }

    public function run(): bool
    {
        $this->container->call($this->callback, $this->parameters);

        return true;
    }
}
