<?php

use Laravel\Horizon\Console\WorkCommand;
use Mockery\MockInterface;
use Qruto\Power\SetupInstructions;

afterEach(fn () => unlink(base_path('config/power.php')));

it('successfully generates setup.php instructions code', function () {
    $setupFileContent = file_get_contents(__DIR__.'/../src/setup.php');

    $this->artisan('power:setup')
        ->expectsOutputToContain('Setup instructions published to [routes/setup.php].')
        ->assertSuccessful();

    $this->assertStringEqualsFile(base_path('routes/setup.php'), $setupFileContent);

    unlink(base_path('routes/setup.php'));
});

it('successfully generates setup.php with discovered packages', function () {
    app()->bind('command.ide-helper.generate', fn () => new stdClass());
    app()->bind(WorkCommand::class, fn () => new stdClass());

    $setupFileContent = file_get_contents(__DIR__.'/TestFixtures/setup-packages.php');

    $this->artisan('power:setup')
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

    $this->artisan('power:setup')
        ->expectsOutputToContain('Setup instructions already exist.')
        ->expectsOutputToContain('Publishing [power-config] assets.')
        ->assertSuccessful();
});
