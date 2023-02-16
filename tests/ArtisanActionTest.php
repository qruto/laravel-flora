<?php

use Illuminate\Support\Facades\Artisan;
use Qruto\Formula\Run;
use Symfony\Component\Console\Command\Command;

it('successfully command artisan commands', function () {
    Artisan::command('some:command', fn () => Command::SUCCESS);

    chain(fn (Run $run) => $run->command('some:command'));

    chain()
        ->run()
        ->expectsOutputToContain('command some:command')
        ->assertSuccessful();
});

it('fails when one of artisan command was failed', function () {
    Artisan::command('some:command', fn () => Command::FAILURE);

    chain(fn (Run $run) => $run->command('some:command'));

    chain()
        ->run()
        ->expectsOutputToContain('command some:command')
        ->assertFailed();
});

it('displays artisan command description in verbose mode', function () {
    Artisan::command('some:command', fn () => Command::SUCCESS)
        ->describe('Some description');

    chain(fn (Run $run) => $run->command('some:command'));

    $this->artisan('update', ['--verbose' => true])
        ->expectsOutputToContain('command some:command Some description')
        ->assertSuccessful();
});

it('doesn\'t display artisan command description in verbose mode', function () {
    Artisan::command('some:command', fn () => Command::SUCCESS)
        ->describe('Some description');

    chain(fn (Run $run) => $run->command('some:command'));

    chain()->run()
        ->expectsOutputToContain('command some:command')
        ->doesntExpectOutputToContain('command some:command (Some description)')
        ->assertSuccessful();
});

it('asks for showing errors when artisan command was failed', function () {
    Artisan::command('some:command', fn () => throw new \Exception('Some exception'));

    chain(fn (Run $run) => $run->command('some:command'));

    chain()->run()
        ->expectsConfirmation('Show errors?', 'yes')
        ->expectsOutputToContain('Some exception')
        ->assertFailed();
});

it('doesn\'t show errors when artisan command was failed and you answer "no" for showing errors', function () {
    Artisan::command('some:command', fn () => throw new \Exception('Some exception'));

    chain(fn (Run $run) => $run->command('some:command'));

    chain()->run()
        ->expectsConfirmation('Show errors?', 'no')
        ->doesntExpectOutputToContain('Some exception')
        ->assertFailed();
});
