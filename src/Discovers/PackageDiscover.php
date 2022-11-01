<?php

namespace Qruto\Initializer\Discovers;

interface PackageDiscover
{
    public function exists(): bool;

    public function instruction(): Instruction;
}
