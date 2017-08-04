<?php

namespace ZFort\Skeleton\Test;

use Orchestra\Testbench\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Contracts\Debug\ExceptionHandler;
use ZFort\Skeleton\Test\Utils\TestExceptionHandler;

class ExampleTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [\ZFort\Skeleton\SkeletonServiceProvider::class];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Skeleton' => \ZFort\Skeleton\SkeletonFacade::class,
        ];
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('avatar');
        });
        include_once __DIR__.'/../database/migrations/create_skeleton_table.php.stub';

        (new \CreateSkeletonTable())->up();

        User::create(['email' => $this->userEmail]);
    }

    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new TestExceptionHandler);
    }
}
