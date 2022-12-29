<?php

namespace Qruto\Initializer\Discovers;

use Illuminate\Support\Facades\Route;
use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Run;

class HorizonDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return Route::has('horizon.index');
    }

    public function instruction(): Instruction
    {
        return new Instruction(
            update: [
                'production' => function (Run $run) {
                    $run->internal->filter(fn (Artisan $action) => $action->getCommand() === 'queue:restart');

                    $run->command('horizon:terminate');
                },
            ],
        );
    }
}
