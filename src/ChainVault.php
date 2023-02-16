<?php

namespace Qruto\Formula;

use Qruto\Formula\Contracts\Chain;
use Qruto\Formula\Contracts\ChainVault as ChainVaultContract;
use Qruto\Formula\Enums\FormulaType;

class ChainVault implements ChainVaultContract
{
    protected array $chains;

    public function __construct(Chain $install, Chain $update)
    {
        $this->chains[FormulaType::Install->value] = $install;
        $this->chains[FormulaType::Update->value] = $update;
    }

    public function get(FormulaType $type): Chain
    {
        return $this->chains[$type->value];
    }
}
