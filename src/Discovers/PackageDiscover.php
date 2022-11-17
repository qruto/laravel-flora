<?php

namespace Qruto\Initializer\Discovers;

// TODO: add support for Laravel Nova
interface PackageDiscover
{
    public function exists(): bool;

    public function instruction(): Instruction;
}
