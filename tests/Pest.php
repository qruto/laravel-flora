<?php

use Illuminate\Support\Facades\App;
use Qruto\Initializer\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function chain(?callable $callback = null): object
{
    if ($callback) {
        App::update('testing', $callback);
    }

    return new class {
        public function run()
        {
            return test()->artisan('update');
        }
    };
}
