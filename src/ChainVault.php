<?php

namespace Qruto\Flora;

use Qruto\Flora\Contracts\Chain;
use Qruto\Flora\Contracts\ChainVault as ChainVaultContract;
use Qruto\Flora\Enums\FloraType;

class ChainVault implements ChainVaultContract
{
    protected array $chains;

    public function __construct(Chain $install, Chain $update)
    {
        $this->chains[FloraType::Install->value] = $install;
        $this->chains[FloraType::Update->value] = $update;
    }

    public function get(FloraType $type): Chain
    {
        return $this->chains[$type->value];
    }
}
