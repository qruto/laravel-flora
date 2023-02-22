<?php

namespace Qruto\Power\Contracts;

use Qruto\Power\Enums\PowerType;

interface ChainVault
{
    public function get(PowerType $type): Chain;
}
