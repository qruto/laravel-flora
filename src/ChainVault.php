<?php

namespace Qruto\Power;

use Qruto\Power\Contracts\Chain;
use Qruto\Power\Contracts\ChainVault as ChainVaultContract;
use Qruto\Power\Enums\PowerType;

class ChainVault implements ChainVaultContract
{
    protected array $chains;

    public function __construct(Chain $install, Chain $update)
    {
        $this->chains[PowerType::Install->value] = $install;
        $this->chains[PowerType::Update->value] = $update;
    }

    public function get(PowerType $type): Chain
    {
        return $this->chains[$type->value];
    }
}
