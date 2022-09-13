<?php

namespace Qruto\Initializer\Contracts;

interface ChainStoreContract
{
    public function saveInstall(ChainContract $chain): ChainContract;

    public function saveUpdate(ChainContract $chain): ChainContract;

    public function getInstall(): ChainContract;

    public function getUpdate(): ChainContract;
}
