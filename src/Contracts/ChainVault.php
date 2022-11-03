<?php

namespace Qruto\Initializer\Contracts;

use Qruto\Initializer\Enums\InitializerType;

interface ChainVault
{
    public function get(InitializerType $type): Chain;
}
