<?php

namespace MadWeb\Initializer\Test;

use Illuminate\Support\Facades\Artisan;

class MakeInitializersCommandTest extends TestCase
{
    /** @test */
    public function installer_config_class_not_exists()
    {
        $this->assertFileNotExists($this->app->path('Install.php'));
        $this->assertFileNotExists($this->app->path('Update.php'));
    }

    /** @test */
    public function create_installer_config_class()
    {
        Artisan::call('make:initializers');

        $this->assertFileExists($this->app->path('Install.php'));
        $this->assertFileExists($this->app->path('Update.php'));

        unlink($this->app->path('Install.php'));
        unlink($this->app->path('Update.php'));
    }
}
