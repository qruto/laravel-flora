<?php

namespace Qruto\Formula\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Qruto\Formula\FormulaServiceProvider;
use Qruto\Formula\Tests\TestFixtures\TestServiceProviderMultipleTags;
use Qruto\Formula\Tests\TestFixtures\TestServiceProviderOne;
use Qruto\Formula\Tests\TestFixtures\TestServiceProviderTwo;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FormulaServiceProvider::class,
            TestServiceProviderOne::class,
            TestServiceProviderTwo::class,
            TestServiceProviderMultipleTags::class,
        ];
    }
}
