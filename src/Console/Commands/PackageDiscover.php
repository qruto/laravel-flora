<?php

namespace Qruto\Formula\Console\Commands;

use Qruto\Formula\Discovers\HorizonDiscover;
use Qruto\Formula\Discovers\IdeHelperDiscover;
use Qruto\Formula\Discovers\VaporUiDiscover;
use Qruto\Formula\Enums\Environment;
use Qruto\Formula\Enums\FormulaType;
use Qruto\Formula\Run;

trait PackageDiscover
{
    protected function discoverPackages(FormulaType $type, string $environment, Run $run): void
    {
        $discovers = self::packagesToDiscover();

        foreach ($discovers as $discover) {
            if ($discover->exists()) {
                $discover->instruction()
                    ->get($type, Environment::tryFrom($environment))($run);
            }
        }
    }

    /**
     * @return \Qruto\Formula\Discovers\PackageDiscover[]
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
