<?php

namespace Qruto\Flora\Console\Commands;

use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;
use Qruto\Flora\Run;

trait PackageInstruction
{
    protected function instructPackages(FloraType $type, string $environment, Run $run): void
    {
        $discovers = resolve('flora.packages');

        foreach ($discovers as $discover) {
            if ($discover->exists()) {
                $discover->instruction()
                    ->get($type, Environment::tryFrom($environment))($run);
            }
        }
    }
}
