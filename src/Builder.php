<?php

namespace Qruto\Initializer;

use Illuminate\Contracts\Container\Container;
use Qruto\Initializer\Contracts\BuilderContract;
use Qruto\Initializer\Contracts\ChainContract;

class Builder implements BuilderContract
{
    protected $installChain;
    protected $updateChain;

    public function __construct(protected Container $container)
    {

    }

    public function install(): ChainContract
    {
        $this->installChain = $this->createCommandChain();

        return $this->installChain;
    }

    public function update(): ChainContract
    {
        $this->updateChain = $this->createCommandChain();

        return $this->updateChain;
    }

    protected function createCommandChain()
    {
        return $this->container->make(ChainContract::class);
    }
}
