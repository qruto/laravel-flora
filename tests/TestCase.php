<?php

namespace Qruto\Flora\Tests;

use NunoMaduro\LaravelDesktopNotifier\LaravelDesktopNotifierServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Qruto\Flora\FloraServiceProvider;
use Qruto\Flora\Tests\TestFixtures\TestServiceProviderMultipleTags;
use Qruto\Flora\Tests\TestFixtures\TestServiceProviderOne;
use Qruto\Flora\Tests\TestFixtures\TestServiceProviderTwo;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDesktopNotifierServiceProvider::class,
            FloraServiceProvider::class,
            TestServiceProviderOne::class,
            TestServiceProviderTwo::class,
            TestServiceProviderMultipleTags::class,
        ];
    }
}
