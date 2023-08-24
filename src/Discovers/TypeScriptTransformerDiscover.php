<?php

namespace Qruto\Flora\Discovers;

use Illuminate\Container\Container;
use Qruto\Flora\Run;

class TypeScriptTransformerDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return Container::getInstance()->has(\Spatie\TypeScriptTransformer\TypeScriptTransformerConfig::class);
    }

    public function instruction(): Instruction
    {
        $command = fn (Run $run): Run => $run->command('typescript:transform');

        return new Instruction(
            install: $command,
            update: $command,
        );
    }
}
