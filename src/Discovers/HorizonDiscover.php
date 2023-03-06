<?php

namespace Qruto\Flora\Discovers;

use Illuminate\Container\Container;
use Qruto\Flora\Actions\Artisan;
use Qruto\Flora\Run;

class HorizonDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return Container::getInstance()->has('Laravel\Horizon\Console\WorkCommand');
    }

    public function instruction(): Instruction
    {
        return new Instruction(
            update: [
                'production' => function (Run $run): void {
                    $run->internal->replace(
                        fn (Artisan $action): bool => $action->name() === 'queue:restart',
                        fn (Run $run): Run => $run->command('horizon:terminate'),
                    );
                },
            ],
        );
    }
}
