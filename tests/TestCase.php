<?php

namespace Qruto\Initializer\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Qruto\Initializer\InitializerServiceProvider;
use Qruto\Initializer\Tests\TestFixtures\TestServiceProviderMultipleTags;
use Qruto\Initializer\Tests\TestFixtures\TestServiceProviderOne;
use Qruto\Initializer\Tests\TestFixtures\TestServiceProviderTwo;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            InitializerServiceProvider::class,
            TestServiceProviderOne::class,
            TestServiceProviderTwo::class,
            TestServiceProviderMultipleTags::class,
        ];
    }
}
