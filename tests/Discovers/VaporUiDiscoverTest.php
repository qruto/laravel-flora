<?php

use Illuminate\Support\Facades\Route;
use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Console\Commands\PackageDiscover;
use Qruto\Initializer\Discovers\IdeHelperDiscover;
use Qruto\Initializer\Discovers\VaporUiDiscover;
use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;

uses(PackageDiscover::class);

beforeEach(function () {
    Route::name('vapor-ui')->get('/vapor-ui', fn () => 'Vapor UI');
});

it('can discover vapor ui', function () {
    $this->assertTrue($this->app->make(VaporUiDiscover::class)->exists());
});

it('no actions defined for any environment', function () {
    $runner = makeRunner();

    $this->discoverPackages(InitializerType::Install, Environment::Local->value, $runner);
    $this->discoverPackages(InitializerType::Install, Environment::Production->value, $runner);
    $this->discoverPackages(InitializerType::Update, Environment::Local->value, $runner);
    $this->discoverPackages(InitializerType::Update, Environment::Production->value, $runner);

    $this->assertCount(0, $runner->internal->getCollection());
});

it('defines publishable asset', function () {
    $assets = [];

    foreach ($this->packagesToDiscover() as $package) {
        if ($package->exists() && $tag = $package->instruction()->assetsTag) {
            $assets[] = $tag;
        }
    }

    $this->assertEquals(['vapor-ui-assets'], $assets);
});
