<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Qruto\Initializer\Run;
use Symfony\Component\Console\Command\Command;

it('successfully running artisan commands', function () {
    Artisan::command('some:command', fn () => Command::SUCCESS);

    App::update('testing', fn (Run $run) => $run->command('some:command'));

    $this->artisan('update')
        ->expectsOutputToContain('Running some:command')
        ->assertSuccessful();
});

it('fails when one of artisan command was failed', function () {
    Artisan::command('some:command', fn () => Command::FAILURE);

    App::update('testing', fn (Run $run) => $run->command('some:command'));

    $this->artisan('update')
        ->expectsOutputToContain('Running some:command')
        ->assertFailed();
});

it('displays artisan command description in verbose mode', function () {
    Artisan::command('some:command', fn () => Command::SUCCESS)
        ->describe('Some description');

    App::update('testing', fn (Run $run) => $run->command('some:command'));

    $this->artisan('update', ['--verbose' => true])
        ->expectsOutputToContain('Running some:command (Some description)')
        ->assertSuccessful();
});

it('doesn\'t display artisan command description in verbose mode', function () {
    Artisan::command('some:command', fn () => Command::SUCCESS)
        ->describe('Some description');

    App::update('testing', fn (Run $run) => $run->command('some:command'));

    $this->artisan('update')
        ->expectsOutputToContain('Running some:command')
        ->doesntExpectOutputToContain('Running some:command (Some description)')
        ->assertSuccessful();
});

it('asks for showing errors when artisan command was failed', function () {
    Artisan::command('some:command', fn () => throw new \Exception('Some exception'));

    App::update('testing', fn (Run $run) => $run->command('some:command'));

    $this->artisan('update')
        ->expectsConfirmation('Show errors?', 'yes')
        ->expectsOutputToContain('Some exception')
        ->assertFailed();
});

it('doesn\'t show errors when artisan command was failed and you answer "no" for showing errors', function () {
    Artisan::command('some:command', fn () => throw new \Exception('Some exception'));

    App::update('testing', fn (Run $run) => $run->command('some:command'));

    $this->artisan('update')
        ->expectsConfirmation('Show errors?', 'no')
        ->doesntExpectOutputToContain('Some exception')
        ->assertFailed();
});
