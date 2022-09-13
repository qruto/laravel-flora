<?php

namespace Qruto\Initializer\Contracts;

interface BuilderContract
{
    public function install(): ChainContract;

    public function update(): ChainContract;
}
