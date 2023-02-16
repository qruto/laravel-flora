<?php

use Illuminate\Support\Facades\Bus;
use Qruto\Formula\Run;
use Qruto\Formula\Tests\TestFixtures\TestJob;
use Qruto\Formula\UndefinedScriptException;

beforeEach(function () {
    Bus::fake();

    Run::newScript(
        'test',
        fn (Run $run) => $run->job(new TestJob())->job(new TestJob())
    );
});

it('throws exception when no scripts found', function () {
    chain(fn (Run $run) => $run->script('non-existing'))
        ->run()
        ->assertFailed()
        ->expectsOutputToContain(UndefinedScriptException::forCustom('non-existing')->getMessage());
});

test('it successfully running script', function () {
    chain(fn (Run $run) => $run->script('test'))
        ->run()
        ->expectsOutputToContain('script test')
        ->doesntExpectOutputToContain('job     Qruto\Formula\Tests\TestFixtures\TestJob')
        ->assertSuccessful();

    Bus::assertDispatched(TestJob::class, 2);
});

test('it verbose successfully running script', function () {
    chain(fn (Run $run) => $run->script('test'), true)
        ->run()
        ->expectsOutputToContain('script test')
        ->expectsOutputToContain('job    Qruto\Formula\Tests\TestFixtures\TestJob')
        ->assertSuccessful();

    Bus::assertDispatched(TestJob::class, 2);
});
