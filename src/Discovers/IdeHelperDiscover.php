<?php

namespace Qruto\Initializer\Discovers;

use Illuminate\Container\Container;
use Qruto\Initializer\Contracts\Runner;

class IdeHelperDiscover implements PackageDiscover
{
    public function exists(): bool
    {
        return Container::getInstance()->has('command.ide-helper.generate');
    }

    public function action(Runner $run): Runner
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
                'local' => static fn(Runner $run) => $run
                        ->command('ide-helper:generate')
                        ->command('ide-helper:meta')
                        ->command('ide-helper:models', ['--nowrite' => true]),
            ],
            [
                'local' => static fn(Runner $run) => $run
                        ->command('ide-helper:generate')
                        ->command('ide-helper:meta')
                        ->command('ide-helper:models', ['--nowrite' => true]),
            ]
        );
    }
}
