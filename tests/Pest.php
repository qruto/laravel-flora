<?php

use Illuminate\Support\Facades\App;
use Qruto\Initializer\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function chain(?callable $callback = null, $verbose = false): object
{
    if ($callback) {
        App::update('testing', $callback);
    }

    return new class($verbose)
    {
        public function __construct(protected bool $verbose)
        {
        }
        public function run($options = [])
        {
            if (!$this->verbose) {
                putenv('SHELL_VERBOSITY=0');
            }

            return test()->artisan('update', $this->verbose ? ['--verbose' => $this->verbose] + $options : $options);
        }
    };
}
