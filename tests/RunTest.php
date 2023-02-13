<?php

use Qruto\Initializer\Actions\ActionTerminatedException;
use Qruto\Initializer\Actions\Callback;
use Symfony\Component\Console\Output\BufferedOutput;

test('run latest action', function () {
    $runner = makeRunner();

    $timesCalled = 0;

    $runner->call(function () use (&$timesCalled) {
        $timesCalled++;
    });

    $runner->internal->start();
    $runner->internal->rerunLatestAction();

    $this->assertEquals(2, $timesCalled);
});

test('writes clear symbols when action has been terminated', function () {
    $output = new BufferedOutput();
    $runner = makeRunner($output);

    $runner->call(fn () => throw new ActionTerminatedException(new Callback($this->app, fn () => true), 0));

    $runner->internal->start();

    $this->assertStringContainsString("DONE\n\x1B[1A\x1B[2K", $output->fetch());
});
