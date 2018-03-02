<?php

namespace MadWeb\Initializer\Test;

use Closure;
use MadWeb\Initializer\Run;
use Illuminate\Support\Facades\Artisan;

abstract class RunnerCommandsTestCase extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->app->bind('project.installer', \MadWeb\Initializer\Test\TestFixtures\TestInstallerConfig::class);
    }

    protected function declareCommands(Closure $callback): void
    {
        $this->app->resolving(Run::class, function (Run $run) use ($callback) {
            /**
             * @todo Remove this after resolving issue
             * @see https://github.com/laravel/framework/pull/23290
             */
            static $is_called = false;

            $is_called ?: $callback($run);

            $is_called = true;
        });
        Artisan::call('app:install');
    }
}
