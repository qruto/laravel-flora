<?php

use Illuminate\Contracts\Cache\Repository;
use Qruto\Initializer\AssetsVersion;

beforeEach(fn () => file_put_contents(
    base_path('composer.lock'),
    json_encode(['content-hash' => 'random-hash']
    )));

afterEach(fn () => unlink(base_path('composer.lock')));

test('assets marked as outdated when current and latest hashes are different', function () {
    $cache = mock(Repository::class)
        ->shouldReceive('get')
        ->with('assets_hash')
        ->andReturn('old-hash')
        ->getMock();

    app()->instance(Repository::class, $cache);

    $this->assertTrue($this->app->make(AssetsVersion::class)->outdated());
});

test('assets marked as new when current and latest hashes are same', function () {
    $cache = mock(Repository::class)
        ->shouldReceive('get')
        ->with('assets_hash')
        ->andReturn('random-hash')
        ->getMock();

    app()->instance(Repository::class, $cache);

    $this->assertTrue(! $this->app->make(AssetsVersion::class)->outdated());
});
