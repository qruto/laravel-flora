<?php

use Qruto\Formula\Run;

class InjectableService
{
}

it('successfully calling a callback', function () {
    $called = false;
    $service = null;

    $callable = function (InjectableService $s) use (&$service, &$called) {
        $called = true;
        $service = $s;

        return true;
    };

    chain(fn (Run $run) => $run->call($callable));

    chain()
        ->run()
        ->expectsOutputToContain('call Closure::__invoke')
        ->assertSuccessful();

    $this->assertTrue($called);
    $this->assertInstanceOf(InjectableService::class, $service);
});

it('displays custom callable name if passed', function () {
    $callable = function () {
        return true;
    };

    chain(fn (Run $run) => $run->call($callable, name: 'Custom Callable Name'));

    chain()
        ->run()
        ->expectsOutputToContain('call Custom Callable Name')
        ->assertSuccessful();
});
