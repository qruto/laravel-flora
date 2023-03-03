<?php

use Illuminate\Console\Signals;
use Qruto\Flora\Console\Commands\UpdateCommand;
use Qruto\Flora\Tests\TestFixtures\FakeSignalsRegistry;

beforeEach(function () {
    Signals::resolveAvailabilityUsing(fn () => true);

    $registry = $this->registry = new FakeSignalsRegistry();

    $this->app->resolving(
        UpdateCommand::class,
        fn ($command) => (fn () => $this->signals = new Signals($registry))->call($command)
    );
});

it('traps an exit signal', function () {
    $pendingCommand = chain(fn ($run) => $run->call(fn () => null)
            ->call(fn () => $this->registry->handle(SIGTERM))
    )->run();

    $pendingCommand
        ->expectsConfirmation('Sure you want to stop', 'yes')
        ->expectsOutputToContain('Update aborted without completion');
});

it('runs latest action after exit signal', function () {
    $this->runCount = 0;

    $pendingCommand = chain(
        fn ($run) => $run
            ->call(fn () => null)
            ->call(function () use (&$actionRunCount) {
                $this->runCount += 1;

                if ($this->runCount === 1) {
                    $this->registry->handle(SIGTERM);
                }
            })
    )->run();

    $pendingCommand
        ->expectsConfirmation('Sure you want to stop', 'no')
        ->run();

    expect($this->runCount)->toBe(2);
});
