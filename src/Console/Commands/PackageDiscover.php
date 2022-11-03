<?php

namespace Qruto\Initializer\Console\Commands;

use Qruto\Initializer\Contracts\Runner;
use Qruto\Initializer\Discovers\HorizonDiscover;
use Qruto\Initializer\Discovers\IdeHelperDiscover;
use Qruto\Initializer\Discovers\TelescopeDiscover;
use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;

trait PackageDiscover
{
    protected function packageDiscovers(InitializerType $type, string $environment, Runner $runner)
    {
        $discovers = [
            new HorizonDiscover(),
            new TelescopeDiscover(),
            new IdeHelperDiscover(),
        ];

        foreach ($discovers as $discover) {
            if ($discover->exists()) {
                $discover->instruction()
                    ->get($type, Environment::tryFrom($environment))($runner);
            }
        }
    }
}
