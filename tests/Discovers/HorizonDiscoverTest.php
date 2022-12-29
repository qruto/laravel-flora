<?php


use Illuminate\Support\Facades\Route;
use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Actions\Instruction;
use Qruto\Initializer\Console\Commands\PackageDiscover;
use Qruto\Initializer\Contracts\ChainVault;
use Qruto\Initializer\Discovers\HorizonDiscover;
use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;

uses(PackageDiscover::class);

beforeEach(function () {
    Route::name('horizon.index')->get('/horizon', fn () => 'Horizon');

    require __DIR__ . '/../../src/build.php';
});

it('can discover horizon', function () {
    $this->assertTrue($this->app->make(HorizonDiscover::class)->exists());
});

it('can get horizon instruction', function () {
    $runner = makeRunner();

    actionNamesForEnvironment(InitializerType::Update, Environment::Production, $runner);

    $this->discoverPackages(InitializerType::Update, Environment::Production->value, $runner);

    $this->assertEquals(
        [
            'cache',
            'migrate',
            'cache:clear',
            'build',
            'horizon:terminate',
        ],
        runnerActionNames($runner),
    );
});

it('has no instructions for install process', function () {
    $runner = makeRunner();

    $this->discoverPackages(InitializerType::Install, Environment::Production->value, $runner);

    $this->assertCount(0, $runner->internal->getCollection());
});

