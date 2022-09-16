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
    protected array $collection = [];

    public function set(string $environment, callable $callback)
    {
        $this->collection[$environment] = $callback;
    }

    public function get(string $environment): callable
    {
        return $this->collection[$environment];
    }
}
