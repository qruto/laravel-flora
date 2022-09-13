<?php

namespace Qruto\Initializer;

use Qruto\Initializer\Contracts\ChainContract;
use Qruto\Initializer\Contracts\ChainStoreContract;

class ChainStore implements ChainStoreContract
{
    protected ChainContract $install;
    protected ChainContract $update;

    public function saveInstall(ChainContract $chain): ChainContract
    {
        $this->install = $chain;

        return $chain;
    }

    public function saveUpdate(ChainContract $chain): ChainContract
    {
        $this->update = $chain;

        return $chain;
    }

    public function getInstall(): ChainContract
    {
        return $this->install;
    }

    public function getUpdate(): ChainContract
    {
        return $this->update;
    }
}
