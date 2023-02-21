<?php

namespace Qruto\Formula\Actions;

use Illuminate\Contracts\Container\Container;

class Callback extends Action
{
    /** Label for callback action */
    public static string $label = 'call';

    /** Show callback action label in cyan color */
    protected string $color = 'cyan';

    public function __construct(
        protected Container $container,
        protected $callback,
        protected array $parameters = [],
        protected ?string $name = null,
    ) {
    }

    /**
     * Get callback action name.
     * A custom value can be set with the $name property.
     */
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

    /** Run callback with service container */
    public function run(): bool
    {
        $this->container->call($this->callback, $this->parameters);

        return true;
    }
}
