<?php

namespace Qruto\Initializer;

use Illuminate\Contracts\Container\Container;
use Qruto\Initializer\Contracts\ChainContract;

/**
 * @method self local(callable $callback)
 * @method self production(callable $callback)
 */
class Chain implements ChainContract
{
    protected array $envCommands = [];

    public function __construct(protected Container $container)
    {
    }

    public function __call($environment, $arguments)
    {
        $this->envCommands[$environment] = $arguments[0];

        return $this;
    }

    public function run(string $environment)
    {
        $this->container->call($this->envCommands[$environment]);
    }
}
