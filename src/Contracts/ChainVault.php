<?php

namespace Qruto\Initializer\Contracts;

interface ChainVault
{
    public function getInstall(): Chain;

    public function getUpdate(): Chain;
}
