<?php

namespace Qruto\Formula\Discovers;

use Illuminate\Support\Facades\Route;
use Qruto\Formula\Actions\Artisan;
use Qruto\Formula\Run;

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
                    $run->internal->filter(fn (Artisan $action) => $action->name() === 'queue:restart');

                    $run->command('horizon:terminate');
                },
            ],
        );
    }
}
