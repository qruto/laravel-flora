<?php

namespace Qruto\Flora\Discovers;

use Illuminate\Container\Container;

class VaporUiDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return Container::getInstance()->has(\Laravel\VaporUi\Console\PublishCommand::class);
    }

    public function instruction(): Instruction
    {
        return new Instruction(
            assetsTag: 'vapor-ui-assets',
        );
    }
}
