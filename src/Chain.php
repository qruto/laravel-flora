<?php

namespace Qruto\Initializer;

use Illuminate\Contracts\Container\Container;
use Qruto\Initializer\Contracts\Chain as ChainContract;

/**
 * @method self local(callable $callback)
 * @method self production(callable $callback)
 */
class Chain implements ChainContract
{
    protected array $envCommands = [];

    public function __call($environment, $arguments)
    {
        $this->envCommands[$environment] = $arguments[0];

        return $this;
    }

    public function getForEnvironment(string $env): callable
    {
        return $this->envCommands[$env];
    }
}
