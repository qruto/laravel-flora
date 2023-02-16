<?php

use Illuminate\Support\Facades\Bus;
use Qruto\Formula\Run;
use Qruto\Formula\Tests\TestFixtures\TestJob;

beforeEach(fn () => Bus::fake());

it('can dispatch a job', function () {
    chain(fn (Run $run) => $run->job(new TestJob('info')));

    chain()->run();

    Bus::assertDispatched(TestJob::class);
});

it('can dispatch a job twice', function () {
    chain(fn (Run $run) => $run->job(new TestJob('info'))->job(new TestJob('info')));

    chain()->run();

    Bus::assertDispatched(TestJob::class, 2);
});
