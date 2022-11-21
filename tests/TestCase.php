<?php

namespace Qruto\Initializer\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Qruto\Initializer\InitializerServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            InitializerServiceProvider::class,
        ];
    }
}
