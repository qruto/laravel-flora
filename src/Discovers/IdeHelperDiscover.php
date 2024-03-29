<?php

namespace Qruto\Flora\Discovers;

use Illuminate\Container\Container;
use Qruto\Flora\Run;

class IdeHelperDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return Container::getInstance()->has('command.ide-helper.generate');
    }

    public function instruction(): Instruction
    {
        return new Instruction(
            install: [
                'local' => static fn (Run $run): Run => $run
                    ->command('ide-helper:generate')
                    ->command('ide-helper:meta')
                    ->command('ide-helper:models', ['--nowrite' => true])
                    ->command('ide-helper:eloquent'),
            ],
            update: [
                'local' => static fn (Run $run): Run => $run
                    ->command('ide-helper:generate')
                    ->command('ide-helper:meta')
                    ->command('ide-helper:models', ['--nowrite' => true])
                    ->command('ide-helper:eloquent'),
            ]
        );
    }
}
