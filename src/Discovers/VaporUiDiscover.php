<?php

namespace Qruto\Formula\Discovers;

use Illuminate\Support\Facades\Route;

class VaporUiDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return Route::has('vapor-ui');
    }

    public function instruction(): Instruction
    {
        return new Instruction(
            assetsTag: 'vapor-ui-assets',
        );
    }
}
