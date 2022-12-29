<?php

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

test('writes clear symbols on action rerun', function () {
    $output = new BufferedOutput();
    $runner = makeRunner($output);

    $runner->call(fn () => true);

    $runner->internal->start();
    $runner->internal->rerunLatestAction();
    $runner->internal->start();

    $this->assertStringContainsString("DONE\n\x1B[1A\x1B[2K", $output->fetch());
});
