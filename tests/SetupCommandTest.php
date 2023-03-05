<?php

use Laravel\Horizon\Console\WorkCommand;
use Mockery\MockInterface;
use Qruto\Flora\SetupInstructions;

beforeEach(fn () => $this->composerJson = file_get_contents(base_path('composer.json')));
afterEach(function () {
    if (file_exists(base_path('config/flora.php'))) {
        unlink(base_path('config/flora.php'));
    }

    file_put_contents(base_path('composer.json'), $this->composerJson);

    if (file_exists(base_path('routes/setup.php'))) {
        unlink(base_path('routes/setup.php'));
    }
});

it('successfully generates setup.php instructions code', function () {
    $setupFileContent = file_get_contents(__DIR__.'/../src/setup.php');

    $this->artisan('flora:setup')
        ->expectsOutputToContain('Setup instructions published to [routes/setup.php]')
        ->assertSuccessful();

    $this->assertStringEqualsFile(base_path('routes/setup.php'), $setupFileContent);
});

it('successfully generates setup.php with discovered packages', function () {
    app()->bind('command.ide-helper.generate', fn () => new stdClass());
    app()->bind(WorkCommand::class, fn () => new stdClass());

    $setupFileContent = file_get_contents(__DIR__.'/TestFixtures/setup-packages.php');

    $this->artisan('flora:setup')
        ->expectsOutputToContain('Setup instructions published to [routes/setup.php].')
        ->assertSuccessful();

    $this->assertStringEqualsFile(base_path('routes/setup.php'), $setupFileContent);

    unlink(base_path('routes/setup.php'));
});

it('show warning if setup.php already exists', function () {
    $this->mock(
        SetupInstructions::class,
        fn (MockInterface $mock) => $mock->shouldReceive('customExists')->once()->andReturnTrue()
    );

    $this->artisan('flora:setup')
        ->expectsOutputToContain('Setup instructions already exist.')
        ->expectsOutputToContain('Publishing [flora-config] assets.')
        ->assertSuccessful();
});

it('add @php artisan update to composer.json scripts post-autoload-dump', function () {
    $this->artisan('flora:setup')
        ->expectsOutputToContain('[@php artisan update] added to post-autoload-dump scripts')
        ->assertSuccessful();

    $composerJsonContent = json_decode(file_get_contents(base_path('composer.json')), true);

    $this->assertContains('@php artisan update', $composerJsonContent['scripts']['post-autoload-dump']);
});
