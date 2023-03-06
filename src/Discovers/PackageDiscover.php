<?php

namespace Qruto\Flora\Discovers;

interface PackageDiscover
{
    public function exists(): bool;

    public function instruction(): Instruction;
}
