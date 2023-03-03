<?php

use Laravel\Horizon\Console\WorkCommand;

afterEach(function () {
    unlink(base_path('routes/setup.php'));
});

it('successfully generates setup.php instructions code', function () {
    $setupFileContent = file_get_contents(__DIR__ . '/../src/setup.php');

    $this->artisan('power:setup')
        ->expectsOutputToContain('Setup instructions published to [routes/setup.php].')
        ->assertSuccessful();

    $this->assertStringEqualsFile(base_path('routes/setup.php'), $setupFileContent);
});

it('successfully generates setup.php with discovered packages', function () {
    app()->bind('command.ide-helper.generate', fn () => new stdClass());
    app()->bind(WorkCommand::class, fn () => new stdClass());

    $setupFileContent = file_get_contents(__DIR__ . '/TestFixtures/setup-packages.php');

    $this->artisan('power:setup')
        ->expectsOutputToContain('Setup instructions published to [routes/setup.php].')
        ->assertSuccessful();

    $this->assertStringEqualsFile(base_path('routes/setup.php'), $setupFileContent);
});
