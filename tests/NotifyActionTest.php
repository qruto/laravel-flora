<?php

use NunoMaduro\LaravelDesktopNotifier\Facades\Notifier;
use Qruto\Power\Run;

it('can send desktop notification', function () {
    Notifier::shouldReceive('send')->once();

    chain(fn (Run $run) => $run->call(fn() => null)->notify('info', 'info'))->run();
});
