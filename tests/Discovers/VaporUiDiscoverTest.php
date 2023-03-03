<?php

use Laravel\VaporUi\Console\PublishCommand;
use Qruto\Flora\Console\Commands\PackageInstruction;
use Qruto\Flora\Discovers\VaporUiDiscover;
use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;

uses(PackageInstruction::class);

beforeEach(function () {
    app()->bind(PublishCommand::class, fn () => new stdClass());
});

it('can discover vapor ui', function () {
    $this->assertTrue($this->app->make(VaporUiDiscover::class)->exists());
});

it('no actions defined for any environment', function () {
    $run = makeRunner();

    $this->instructPackages(FloraType::Install, Environment::Local->value, $run);
    $this->instructPackages(FloraType::Install, Environment::Production->value, $run);
    $this->instructPackages(FloraType::Update, Environment::Local->value, $run);
    $this->instructPackages(FloraType::Update, Environment::Production->value, $run);

    $this->assertCount(0, $run->internal->getCollection());
});

it('defines publishable asset', function () {
    $assets = [];

    foreach ($this->app['flora.packages'] as $package) {
        if ($package->exists() && $tag = $package->instruction()->assetsTag) {
            $assets[] = $tag;
        }
    }

    $this->assertEquals(['vapor-ui-assets'], $assets);
});
