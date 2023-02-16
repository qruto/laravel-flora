<?php

namespace Qruto\Formula\Discovers;

use Illuminate\Container\Container;
use Qruto\Formula\Run;

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
                'local' => static fn (Run $run) => $run
                        ->command('ide-helper:generate')
                        ->command('ide-helper:meta')
                        ->command('ide-helper:models', ['--nowrite' => true]),
            ],
            update: [
                'local' => static fn (Run $run) => $run
                        ->command('ide-helper:generate')
                        ->command('ide-helper:meta')
                        ->command('ide-helper:models', ['--nowrite' => true]),
            ]
        );
    }
}
