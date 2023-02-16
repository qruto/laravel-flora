<?php

namespace Qruto\Formula\Contracts;

use Qruto\Formula\Enums\FormulaType;

interface ChainVault
{
    public function get(FormulaType $type): Chain;
}
