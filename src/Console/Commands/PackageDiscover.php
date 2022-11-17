<?php

namespace Qruto\Initializer\Console\Commands;

use Qruto\Initializer\Contracts\Runner;
use Qruto\Initializer\Discovers\HorizonDiscover;
use Qruto\Initializer\Discovers\IdeHelperDiscover;
use Qruto\Initializer\Discovers\VaporUiDiscover;
use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;

trait PackageDiscover
{
    protected function discoverPackages(InitializerType $type, string $environment, Runner $runner): void
    {
        $discovers = self::packagesToDiscover();

        foreach ($discovers as $discover) {
            if ($discover->exists()) {
                $discover->instruction()
                    ->get($type, Environment::tryFrom($environment))($runner);
            }
        }
    }

    /**
     * @return \Qruto\Initializer\Discovers\PackageDiscover[]
     */
    protected static function packagesToDiscover(): array
    {
        static $discovers = null;

        return $discovers ?? $discovers = [
            new VaporUiDiscover(),
            new HorizonDiscover(),
            new IdeHelperDiscover(),
        ];
    }
}
