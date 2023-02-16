<?php

use Illuminate\Support\Facades\Artisan;
use Qruto\Formula\Run;
use Qruto\Formula\UndefinedScriptException;
use Symfony\Component\Console\Command\Command;

it('throws exception when no instructions found for current test environment',
    function () {
        chain()
            ->run()
            ->assertFailed()
            ->expectsOutputToContain(UndefinedScriptException::forEnvironment('testing')->getMessage());
    }
);

it('adopts action name column width by extra spaces', function () {
    Artisan::command('some:command', fn () => Command::SUCCESS);

    chain(fn (Run $run) => $run->command('some:command')->call(fn () => true, name: 'test-call'))
        ->run()
        ->expectsOutputToContain('command some:command')
        ->expectsOutputToContain('call    test-call')
        ->assertSuccessful();
});
