<?php

use Laravel\Horizon\Console\WorkCommand;
use Qruto\Formula\Console\Commands\PackageDiscover;
use Qruto\Formula\Discovers\HorizonDiscover;
use Qruto\Formula\Enums\Environment;
use Qruto\Formula\Enums\FormulaType;

uses(PackageDiscover::class);

beforeEach(function () {
    app()->bind(WorkCommand::class, fn () => new stdClass());

    require __DIR__.'/../../src/setup.php';
});

it('can discover horizon', function () {
    $this->assertTrue($this->app->make(HorizonDiscover::class)->exists());
});

it('can get horizon instruction', function () {
    $run = makeRunner();

    actionNamesForEnvironment(FormulaType::Update, Environment::Production, $run);

    $this->discoverPackages(FormulaType::Update, Environment::Production->value, $run);

    $this->assertEquals(
        [
            'cache',
            'migrate',
            'cache:clear',
            'build',
            'horizon:terminate',
        ],
        runnerActionNames($run),
    );
});

it('has no instructions for install process', function () {
    $run = makeRunner();

    $this->discoverPackages(FormulaType::Install, Environment::Production->value, $run);

    $this->assertCount(0, $run->internal->getCollection());
});
