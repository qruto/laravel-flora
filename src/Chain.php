<?php

namespace Qruto\Initializer;

use Qruto\Initializer\Contracts\Chain as ChainContract;

class Chain implements ChainContract
{
    protected array $collection = [];

    public function set(string $environment, callable $callback): void
    {
        $this->collection[$environment] = $callback;
    }

    public function get(string $environment): callable
    {
        if (! array_key_exists($environment, $this->collection)) {
            throw new UndefinedInstructionException($environment);
        }

        return $this->collection[$environment];
    }
}
