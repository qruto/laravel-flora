<?php

namespace Qruto\Initializer;

use Qruto\Initializer\Contracts\Chain;
use Qruto\Initializer\Contracts\ChainVault as ChainVaultContract;
use Qruto\Initializer\Enums\InitializerType;

class ChainVault implements ChainVaultContract
{
    protected array $chains;

    public function __construct(Chain $install, Chain $update)
    {
        $this->chains[InitializerType::Install->value] = $install;
        $this->chains[InitializerType::Update->value] = $update;
    }

    public function get(InitializerType $type): Chain
    {
        return $this->chains[$type->value];
    }
}
