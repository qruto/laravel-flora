<?php

namespace Qruto\Formula\Test;

use Illuminate\Contracts\Cache\Repository;
use Qruto\Formula\Run;
use Qruto\Formula\Tests\TestFixtures\TestServiceProviderMultipleTags;
use Qruto\Formula\Tests\TestFixtures\TestServiceProviderOne;
use Qruto\Formula\Tests\TestFixtures\TestServiceProviderTwo;
use function unlink;

afterEach(function () {
    $assetPathOne = public_path('asset-one.txt');
    $assetPathTwo = public_path('asset-two.txt');

    if (file_exists($assetPathOne)) {
        unlink($assetPathOne);
    }

    if (file_exists($assetPathTwo)) {
        unlink($assetPathTwo);
    }
});
function prepare(array $assets): object
{
    config()->set('formula.assets', $assets);

    return new class(chain(fn (Run $run) => $run->call(fn () => true))->run())
    {
        public string $assetOnePath;

        public string $assetTwoPath;

        public function __construct(
            protected $test,
        ) {
            $this->assetOnePath = public_path('asset-one.txt');
            $this->assetTwoPath = public_path('asset-two.txt');
        }

        public function assertAssetOnePublished(): self
        {
            unset($this->test);

            test()->assertFileExists($this->assetOnePath);

            return $this;
        }

        public function assertAssetTwoPublished(): self
        {
            unset($this->test);

            test()->assertFileExists($this->assetTwoPath);

            return $this;
        }

        public function assertAllAssetsPublished(): self
        {
            unset($this->test);

            test()->assertFileExists($this->assetOnePath);
            test()->assertFileExists($this->assetTwoPath);

            return $this;
        }

        public function assertNoAssetsPublished(): self
        {
            unset($this->test);

            test()->assertFileDoesNotExist($this->assetTwoPath);
            test()->assertFileDoesNotExist($this->assetOnePath);

            return $this;
        }

        public function test()
        {
            return $this->test;
        }
    };
}

it('successfully publishes a single service provider', fn () => prepare([TestServiceProviderOne::class])->assertAssetOnePublished());

it('successfully publishes two service provider', fn () => prepare([
    TestServiceProviderOne::class => 'public',
    TestServiceProviderTwo::class => 'public',
])->assertAllAssetsPublished());

it('successfully publishes a single tag', fn () => prepare(['one'])->assertAssetOnePublished());

it('successfully publishes single service provider with tag string', function () {
    $core = prepare([
        TestServiceProviderMultipleTags::class => 'one',
    ])->assertAssetOnePublished();

    $this->assertFileDoesNotExist($core->assetTwoPath);
});

it(
    'successfully publishes single service provider with tags array',
    fn () => prepare([
        TestServiceProviderMultipleTags::class => ['one', 'two'],
    ])->assertAllAssetsPublished()
);

it(
    'throws exception when service provider does not exist',
    fn () => prepare(['NonExistingServiceProvider'])->assertNoAssetsPublished()
);

it('don\'t publish assets when latest present', function () {
    $composerLockPath = base_path('composer.lock');

    file_put_contents(
        $composerLockPath,
        json_encode(['content-hash' => 'random-hash']
        ));

    $cache = mock(Repository::class)
        ->shouldReceive('get')
        ->with('assets_hash')
        ->andReturn('random-hash')
        ->getMock();

    $cache->shouldReceive('put')->withAnyArgs();

    app()->instance(Repository::class, $cache);

    $chain = prepare([
        TestServiceProviderMultipleTags::class => ['one', 'two'],
    ]);

    $chain->test()->expectsOutputToContain('No assets for publishing');
    $chain->assertNoAssetsPublished();

    unlink($composerLockPath);
});
