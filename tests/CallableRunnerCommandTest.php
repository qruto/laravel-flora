<?php

use Illuminate\Support\Facades\App;
use Qruto\Initializer\Run;

class InjectableService
{
}

it('successfully running artisan commands', function () {
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
        ->expectsOutputToContain('Calling Closure::__invoke')
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
        ->expectsOutputToContain('Calling Custom Callable Name')
        ->assertSuccessful();
});
