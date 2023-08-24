<?php

use Illuminate\Support\Facades\Artisan;
use Qruto\Flora\Actions\Artisan as ArtisanAction;
use Qruto\Flora\Console\Commands\PackageInstruction;
use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;

uses(PackageInstruction::class);

beforeEach(function () {
    Artisan::command('trail:generate', fn () => null);
});

it('successfully instruct trail:generate for local environment', function () {
    $run = makeRunner();

    $this->instructPackages(FloraType::Install, Environment::Local->value, $run);

    $this->assertCount(1, $run->internal->getCollection());
    $this->assertContainsOnlyInstancesOf(ArtisanAction::class, $run->internal->getCollection());
    $this->assertEquals(
        [
            'trail:generate',
        ],
        collect($run->internal->getCollection())
            ->map(fn ($action) => $action->name())
            ->toArray()
    );
});

it('successfully instruct trail:generate for production environment', function () {
    $run = makeRunner();

    $this->instructPackages(FloraType::Install, Environment::Production->value, $run);

    $this->assertCount(1, $run->internal->getCollection());
    $this->assertContainsOnlyInstancesOf(ArtisanAction::class, $run->internal->getCollection());
    $this->assertEquals(
        [
            'trail:generate',
        ],
        collect($run->internal->getCollection())
            ->map(fn ($action) => $action->name())
            ->toArray()
    );
});
