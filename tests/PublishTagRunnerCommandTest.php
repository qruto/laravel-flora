<?php

namespace MadWeb\Initializer\Test;

use InvalidArgumentException;
use MadWeb\Initializer\Run;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderMultipleTags;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderOne;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderTwo;

class PublishTagRunnerCommandTest extends RunnerCommandsTestCase
{
    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function by_array($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publishTag(['one', 'two']);
        }, $command);

        $public_path_to_file_one = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file_one);

        unlink($public_path_to_file_one);

        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_two);

        unlink($public_path_to_file_two);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function by_string($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publishTag('one');
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function force_by_string($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publishTag('one');
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        $last_update = filectime($public_path_to_file);

        // Need for changing last modified time of publishable file
        sleep(1);
        $this->declareCommands(function (Run $run) {
            $run->publishTagForce('one');
        }, $command);

        clearstatcache();

        $this->assertTrue($last_update < filectime($public_path_to_file));

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function force_by_array($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publishTag(['one']);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        $last_update = filectime($public_path_to_file);

        // Need for changing last modified time of publishable file
        sleep(1);
        $this->declareCommands(function (Run $run) {
            $run->publishTagForce(['one']);
        }, $command);

        clearstatcache();

        $this->assertTrue($last_update < filectime($public_path_to_file));

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function exception_on_invalid_tag($command)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->declareCommands(function (Run $run) {
            $run->publish(true);
        }, $command);
    }

    protected function getPackageProviders($app)
    {
        $providers = parent::getPackageProviders($app);

        $providers[] = TestServiceProviderOne::class;
        $providers[] = TestServiceProviderTwo::class;
        $providers[] = TestServiceProviderMultipleTags::class;

        return $providers;
    }
}
