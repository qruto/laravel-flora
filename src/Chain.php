<?php

namespace Qruto\Formula;

use Qruto\Formula\Contracts\Chain as ChainContract;

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
            throw UndefinedScriptException::forEnvironment($environment);
        }

        return $this->collection[$environment];
    }
}
