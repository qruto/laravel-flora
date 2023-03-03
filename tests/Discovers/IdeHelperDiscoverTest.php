<?php

use Qruto\Flora\Actions\Artisan;
use Qruto\Flora\Console\Commands\PackageInstruction;
use Qruto\Flora\Discovers\IdeHelperDiscover;
use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;

uses(PackageInstruction::class);

beforeEach(function () {
    app()->bind('command.ide-helper.generate', fn () => new stdClass());
});

it('can discover ide helper', function () {
    $this->assertTrue($this->app->make(IdeHelperDiscover::class)->exists());
});

it('can get ide helper instruction', function () {
    $run = makeRunner();

    $this->instructPackages(FloraType::Install, Environment::Local->value, $run);

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

    $this->instructPackages(FloraType::Install, Environment::Production->value, $run);

    $this->assertCount(0, $run->internal->getCollection());
});
