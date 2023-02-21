<?php

use Laravel\VaporUi\Console\PublishCommand;
use Qruto\Formula\Console\Commands\PackageDiscover;
use Qruto\Formula\Discovers\VaporUiDiscover;
use Qruto\Formula\Enums\Environment;
use Qruto\Formula\Enums\FormulaType;

uses(PackageDiscover::class);

beforeEach(function () {
    app()->bind(PublishCommand::class, fn () => new stdClass());
});

it('can discover vapor ui', function () {
    $this->assertTrue($this->app->make(VaporUiDiscover::class)->exists());
});

it('no actions defined for any environment', function () {
    $run = makeRunner();

    $this->discoverPackages(FormulaType::Install, Environment::Local->value, $run);
    $this->discoverPackages(FormulaType::Install, Environment::Production->value, $run);
    $this->discoverPackages(FormulaType::Update, Environment::Local->value, $run);
    $this->discoverPackages(FormulaType::Update, Environment::Production->value, $run);

    $this->assertCount(0, $run->internal->getCollection());
});

it('defines publishable asset', function () {
    $assets = [];

    foreach ($this->app['formula.packages'] as $package) {
        if ($package->exists() && $tag = $package->instruction()->assetsTag) {
            $assets[] = $tag;
        }
    }

    $this->assertEquals(['vapor-ui-assets'], $assets);
});
