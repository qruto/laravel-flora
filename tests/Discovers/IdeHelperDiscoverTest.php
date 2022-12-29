<?php

use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Console\Commands\PackageDiscover;
use Qruto\Initializer\Discovers\IdeHelperDiscover;
use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;

uses(PackageDiscover::class);


beforeEach(function () {
    app()->bind('command.ide-helper.generate', fn () => new stdClass());
});

it('can discover ide helper', function () {
    $this->assertTrue($this->app->make(IdeHelperDiscover::class)->exists());
});

it('can get ide helper instruction', function () {
    $runner = makeRunner();

    $this->discoverPackages(InitializerType::Install, Environment::Local->value, $runner);

    $this->assertCount(3, $runner->internal->getCollection());
    $this->assertContainsOnlyInstancesOf(Artisan::class, $runner->internal->getCollection());
    $this->assertEquals(
        [
            'ide-helper:generate',
            'ide-helper:meta',
            'ide-helper:models',
        ],
        collect($runner->internal->getCollection())
            ->map(fn ($action) => $action->getCommand())
            ->toArray()
    );
});

it('has no instructions for production environment', function () {
    $runner = makeRunner();

    $this->discoverPackages(InitializerType::Install, Environment::Production->value, $runner);

    $this->assertCount(0, $runner->internal->getCollection());
});
