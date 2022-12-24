<?php

use Illuminate\Support\Facades\Bus;
use Qruto\Initializer\Run;
use Qruto\Initializer\Tests\TestFixtures\TestJob;
use Qruto\Initializer\UndefinedInstructionException;

beforeEach(function () {
    Bus::fake();

    Run::newInstruction(
        'test',
        fn (Run $run) => $run->job(new TestJob())->job(new TestJob())
    );
});

it('throws exception when no instructions', function () {
    chain(fn (Run $run) => $run->instruction('non-existing'))
        ->run()
        ->assertFailed()
        ->expectsOutputToContain(UndefinedInstructionException::forCustom('non-existing')->getMessage());
});

test('it successfully running instruction', function () {
    chain(fn (Run $run) => $run->instruction('test'))
        ->run()
        ->expectsOutputToContain('Performing test')
        ->doesntExpectOutputToContain('Dispatching Qruto\Initializer\Tests\TestFixtures\TestJob')
        ->assertSuccessful();

    Bus::assertDispatched(TestJob::class, 2);
});

test('it verbose successfully running instruction', function () {
    chain(fn (Run $run) => $run->instruction('test'), true)
        ->run()
        ->expectsOutputToContain('Performing test')
        ->expectsOutputToContain('Dispatching Qruto\Initializer\Tests\TestFixtures\TestJob')
        ->assertSuccessful();

    Bus::assertDispatched(TestJob::class, 2);
});
