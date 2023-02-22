<?php

use Laravel\VaporUi\Console\PublishCommand;
use Qruto\Power\Console\Commands\PackageDiscover;
use Qruto\Power\Discovers\VaporUiDiscover;
use Qruto\Power\Enums\Environment;
use Qruto\Power\Enums\PowerType;

uses(PackageDiscover::class);

beforeEach(function () {
    app()->bind(PublishCommand::class, fn () => new stdClass());
});

it('can discover vapor ui', function () {
    $this->assertTrue($this->app->make(VaporUiDiscover::class)->exists());
});

it('no actions defined for any environment', function () {
    $run = makeRunner();

    $this->discoverPackages(PowerType::Install, Environment::Local->value, $run);
    $this->discoverPackages(PowerType::Install, Environment::Production->value, $run);
    $this->discoverPackages(PowerType::Update, Environment::Local->value, $run);
    $this->discoverPackages(PowerType::Update, Environment::Production->value, $run);

    $this->assertCount(0, $run->internal->getCollection());
});

it('defines publishable asset', function () {
    $assets = [];

    foreach ($this->app['power.packages'] as $package) {
        if ($package->exists() && $tag = $package->instruction()->assetsTag) {
            $assets[] = $tag;
        }
    }

    $this->assertEquals(['vapor-ui-assets'], $assets);
});
