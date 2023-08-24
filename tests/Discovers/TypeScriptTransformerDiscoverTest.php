<?php

use Qruto\Flora\Actions\Artisan;
use Qruto\Flora\Console\Commands\PackageInstruction;
use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;

uses(PackageInstruction::class);

beforeEach(function () {
    app()->bind(\Spatie\TypeScriptTransformer\TypeScriptTransformerConfig::class, fn () => new stdClass());
});

it('successfully instruct typescript:transform for local environment', function () {
    $run = makeRunner();

    $this->instructPackages(FloraType::Install, Environment::Local->value, $run);

    $this->assertCount(1, $run->internal->getCollection());
    $this->assertContainsOnlyInstancesOf(Artisan::class, $run->internal->getCollection());
    $this->assertEquals(
        [
            'typescript:transform',
        ],
        collect($run->internal->getCollection())
            ->map(fn ($action) => $action->name())
            ->toArray()
    );
});

it('successfully instruct typescript:transform for production environment', function () {
    $run = makeRunner();

    $this->instructPackages(FloraType::Install, Environment::Production->value, $run);

    $this->assertCount(1, $run->internal->getCollection());
    $this->assertContainsOnlyInstancesOf(Artisan::class, $run->internal->getCollection());
    $this->assertEquals(
        [
            'typescript:transform',
        ],
        collect($run->internal->getCollection())
            ->map(fn ($action) => $action->name())
            ->toArray()
    );
});
