<?php

namespace Qruto\Power\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Qruto\Power\PowerServiceProvider;
use Qruto\Power\Tests\TestFixtures\TestServiceProviderMultipleTags;
use Qruto\Power\Tests\TestFixtures\TestServiceProviderOne;
use Qruto\Power\Tests\TestFixtures\TestServiceProviderTwo;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PowerServiceProvider::class,
            TestServiceProviderOne::class,
            TestServiceProviderTwo::class,
            TestServiceProviderMultipleTags::class,
        ];
    }
}
