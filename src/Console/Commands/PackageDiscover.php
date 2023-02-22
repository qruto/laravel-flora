<?php

namespace Qruto\Power\Console\Commands;

use Qruto\Power\Enums\Environment;
use Qruto\Power\Enums\PowerType;
use Qruto\Power\Run;

trait PackageDiscover
{
    protected function discoverPackages(PowerType $type, string $environment, Run $run): void
    {
        $discovers = resolve('power.packages');

        foreach ($discovers as $discover) {
            if ($discover->exists()) {
                $discover->instruction()
                    ->get($type, Environment::tryFrom($environment))($run);
            }
        }
    }
}
