<?php

namespace Qruto\Flora\Discovers;

use Illuminate\Support\Facades\Artisan;
use Qruto\Flora\Run;

class TrailDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return collect(Artisan::all())->has('trail:generate');
    }

    public function instruction(): Instruction
    {
        $command = fn (Run $run): Run => $run->command('trail:generate');

        return new Instruction(
            install: $command,
            update: $command,
        );
    }
}
