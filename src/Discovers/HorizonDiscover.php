<?php

namespace Qruto\Initializer\Discovers;

use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Contracts\Runner;

class HorizonDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return defined('HORIZON_PATH');
    }

    public function instruction(): Instruction
    {
        return new Instruction(
            update: [
                'local' => fn (Runner $run) => $run->command('horizon:publish'),

                'production' => function (Runner $run) {
                    $run->internal->filter(fn (Artisan $action) => $action->getCommand() === 'queue:restart');

                    $run->command('horizon:terminate');
                },
            ],
        );
    }
}
