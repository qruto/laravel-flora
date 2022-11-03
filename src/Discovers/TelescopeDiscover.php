<?php

namespace Qruto\Initializer\Discovers;

use Qruto\Initializer\Contracts\Runner;

class TelescopeDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return config('telescope.enabled') !== null;
    }

    public function instruction(): Instruction
    {
        return new Instruction(
            update: ['local' => static fn(Runner $run) => $run->command('telescope:publish')],
        );
    }
}
