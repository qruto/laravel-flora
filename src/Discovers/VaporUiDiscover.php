<?php

namespace Qruto\Formula\Discovers;

use Illuminate\Container\Container;
use Laravel\VaporUi\Console\PublishCommand;

class VaporUiDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return Container::getInstance()->has(PublishCommand::class);
    }

    public function instruction(): Instruction
    {
        return new Instruction(
            assetsTag: 'vapor-ui-assets',
        );
    }
}
