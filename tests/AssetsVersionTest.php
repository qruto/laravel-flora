<?php

use Illuminate\Contracts\Cache\Repository;
use Qruto\Formula\AssetsVersion;

beforeEach(fn () => file_put_contents(
    base_path('composer.lock'),
    json_encode(['content-hash' => 'random-hash']
    )));

afterEach(function () {
    $composerLockPath = base_path('composer.lock');

    if (! file_exists($composerLockPath)) {
        return;
    }

    unlink($composerLockPath);
});

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

test('assets marked as outdated when latest hash is null', function () {
    $cache = mock(Repository::class)
        ->shouldReceive('get')
        ->with('assets_hash')
        ->andReturn(null)
        ->getMock();

    app()->instance(Repository::class, $cache);

    $this->assertTrue($this->app->make(AssetsVersion::class)->outdated());
});

test('assets marked as outdated when current hash is null', function () {
    unlink(base_path('composer.lock'));

    $cache = mock(Repository::class)
        ->shouldReceive('get')
        ->with('assets_hash')
        ->andReturn('random-hash')
        ->getMock();

    app()->instance(Repository::class, $cache);

    $this->assertTrue($this->app->make(AssetsVersion::class)->outdated());
});
