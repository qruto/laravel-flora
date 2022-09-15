<?php

namespace Qruto\Initializer;

use Qruto\Initializer\Contracts\Chain;
use Qruto\Initializer\Contracts\ChainVault as ChainVaultContract;

class ChainVault implements ChainVaultContract
{
    public function __construct(protected Chain $install, protected Chain $update)
    {
    }

    public function getInstall(): Chain
    {
        return $this->install;
    }

    public function getUpdate(): Chain
    {
        return $this->update;
    }
}
