<?php

namespace Qruto\Formula\Console\Commands;

use Qruto\Formula\Enums\Environment;
use Qruto\Formula\Enums\FormulaType;
use Qruto\Formula\Run;

trait PackageDiscover
{
    protected function discoverPackages(FormulaType $type, string $environment, Run $run): void
    {
        $discovers = resolve('formula.packages');

        foreach ($discovers as $discover) {
            if ($discover->exists()) {
                $discover->instruction()
                    ->get($type, Environment::tryFrom($environment))($run);
            }
        }
    }
}
