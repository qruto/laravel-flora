<?php

namespace Qruto\Flora\Contracts;

use Qruto\Flora\Enums\FloraType;

interface ChainVault
{
    public function get(FloraType $type): Chain;
}
