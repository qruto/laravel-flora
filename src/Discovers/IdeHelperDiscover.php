<?php

namespace Qruto\Initializer\Discovers;

use Illuminate\Container\Container;
use Qruto\Initializer\Run;

class IdeHelperDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return Container::getInstance()->has('command.ide-helper.generate');
    }

    public function action(Run $run): Run
    {
        return $run
            ->command('ide-helper:generate')
            ->command('ide-helper:meta')
            ->command('ide-helper:models', ['--nowrite' => true]);
    }

    public function instruction(): Instruction
    {
        return new Instruction(
            [
                'local' => static fn (Run $run) => $run
                        ->command('ide-helper:generate')
                        ->command('ide-helper:meta')
                        ->command('ide-helper:models', ['--nowrite' => true]),
            ],
            [
                'local' => static fn (Run $run) => $run
                        ->command('ide-helper:generate')
                        ->command('ide-helper:meta')
                        ->command('ide-helper:models', ['--nowrite' => true]),
            ]
        );
    }
}
