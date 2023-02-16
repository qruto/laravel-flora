<?php

use Qruto\Formula\Actions\ActionTerminatedException;
use Qruto\Formula\Actions\Callback;
use Symfony\Component\Console\Output\BufferedOutput;

test('run latest action', function () {
    $run = makeRunner();

    $timesCalled = 0;

    $run->call(function () use (&$timesCalled) {
        $timesCalled++;
    });

    $run->internal->start();
    $run->internal->rerunLatestAction();

    $this->assertEquals(2, $timesCalled);
});

test('writes clear symbols when action has been terminated', function () {
    $output = new BufferedOutput();
    $run = makeRunner($output);

    $run->call(fn () => throw new ActionTerminatedException(new Callback($this->app, fn () => true), 0));

    $run->internal->start();

    $this->assertStringContainsString("DONE\n\x1B[1A\x1B[2K", $output->fetch());
});
