<?php

namespace MadWeb\Initializer\Test;

use Illuminate\Support\Facades\Artisan;

class MakeInstallerCommandTest extends TestCase
{
    /** @test */
    public function installer_config_class_not_exists()
    {
        $this->assertFileNotExists($this->app->path('InstallerConfig.php'));
    }

    /** @test */
    public function create_installer_config_class()
    {
        Artisan::call('make:installer');

        $this->assertFileExists($this->app->path('InstallerConfig.php'));

        unlink($this->app->path('InstallerConfig.php'));
    }
}
