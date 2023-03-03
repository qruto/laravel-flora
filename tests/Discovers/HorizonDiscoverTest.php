<?php

use Laravel\Horizon\Console\WorkCommand;
use Qruto\Flora\Console\Commands\PackageInstruction;
use Qruto\Flora\Discovers\HorizonDiscover;
use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;
use Qruto\Flora\SetupInstructions;

uses(PackageInstruction::class);

beforeEach(function () {
    app()->bind(WorkCommand::class, fn () => new stdClass());

    $this->app[SetupInstructions::class]->loadDefault();
});

it('can discover horizon', function () {
    $this->assertTrue($this->app->make(HorizonDiscover::class)->exists());
});

it('can get horizon instruction', function () {
    $run = makeRunner();

    actionNamesForEnvironment(FloraType::Update, Environment::Production, $run);

    $this->instructPackages(FloraType::Update, Environment::Production->value, $run);

    $this->assertEquals(
        [
            'cache',
            'migrate',
            'cache:clear',
            'horizon:terminate',
            'build',
        ],
        runnerActionNames($run),
    );
});

it('has no instructions for install process', function () {
    $run = makeRunner();

    $this->instructPackages(FloraType::Install, Environment::Production->value, $run);

    $this->assertCount(0, $run->internal->getCollection());
});
