<?php

namespace Qruto\Initializer;

use Illuminate\Contracts\Container\Container;
use Qruto\Initializer\Contracts\BuilderContract;
use Qruto\Initializer\Contracts\ChainContract;
use Qruto\Initializer\Contracts\ChainStoreContract;

class Builder implements BuilderContract
{
    public function __construct(protected Container $container, public ChainStoreContract $store)
    {
    }

    public function install(): ChainContract
    {
        $chain = $this->store->saveInstall($this->createCommandChain());

        return $chain;
    }

    public function update(): ChainContract
    {
        $chain = $this->store->saveUpdate($this->createCommandChain());

        return $chain;
    }

    protected function createCommandChain()
    {
        return $this->container->make(ChainContract::class);
    }
}
