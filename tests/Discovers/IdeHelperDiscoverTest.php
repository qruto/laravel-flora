<?php

use Qruto\Formula\Actions\Artisan;
use Qruto\Formula\Console\Commands\PackageDiscover;
use Qruto\Formula\Discovers\IdeHelperDiscover;
use Qruto\Formula\Enums\Environment;
use Qruto\Formula\Enums\FormulaType;

uses(PackageDiscover::class);

beforeEach(function () {
    app()->bind('command.ide-helper.generate', fn () => new stdClass());
});

it('can discover ide helper', function () {
    $this->assertTrue($this->app->make(IdeHelperDiscover::class)->exists());
});

it('can get ide helper instruction', function () {
    $run = makeRunner();

    $this->discoverPackages(FormulaType::Install, Environment::Local->value, $run);

    $this->assertCount(3, $run->internal->getCollection());
    $this->assertContainsOnlyInstancesOf(Artisan::class, $run->internal->getCollection());
    $this->assertEquals(
        [
            'ide-helper:generate',
            'ide-helper:meta',
            'ide-helper:models',
        ],
        collect($run->internal->getCollection())
            ->map(fn ($action) => $action->name())
            ->toArray()
    );
});

it('has no instructions for production environment', function () {
    $run = makeRunner();

    $this->discoverPackages(FormulaType::Install, Environment::Production->value, $run);

    $this->assertCount(0, $run->internal->getCollection());
});
