<?php

namespace MadWeb\Initializer\Test;

use InvalidArgumentException;
use MadWeb\Initializer\Run;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderMultipleTags;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderOne;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderTwo;

class PublishRunnerCommandTest extends RunnerCommandsTestCase
{
    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function by_array($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class]);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function by_string($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish(TestServiceProviderOne::class);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function with_tag($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class => 'public']);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function with_multiple_tags($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderMultipleTags::class => ['one', 'two'],
            ]);
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
    public function with_wrong_tag($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class => 'wrong-tag']);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileDoesNotExist($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function multiple_providers($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderOne::class,
                TestServiceProviderTwo::class,
            ]);
        }, $command);

        $public_path_to_file_one = public_path('test-publishable-one.txt');
        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_one);
        $this->assertFileExists($public_path_to_file_two);

        unlink($public_path_to_file_one);
        unlink($public_path_to_file_two);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function multiple_providers_with_tags($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderOne::class => 'public',
                TestServiceProviderTwo::class => 'public',
            ]);
        }, $command);

        $public_path_to_file_one = public_path('test-publishable-one.txt');
        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_one);
        $this->assertFileExists($public_path_to_file_two);

        unlink($public_path_to_file_one);
        unlink($public_path_to_file_two);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function multiple_providers_with_one_wrong_tag($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderOne::class => 'public',
                TestServiceProviderTwo::class => 'wrong-tag',
            ]);
        }, $command);

        $public_path_to_file_one = public_path('test-publishable-one.txt');
        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_one);
        $this->assertFileDoesNotExist($public_path_to_file_two);

        unlink($public_path_to_file_one);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function force_by_string($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->publish(TestServiceProviderOne::class);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        $last_update = filectime($public_path_to_file);

        // Need for changing last modified time of publishable file
        sleep(1);
        $this->declareCommands(function (Run $run) {
            $run->publishForce(TestServiceProviderOne::class);
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
            $run->publish([TestServiceProviderOne::class]);
        }, $command);

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        $last_update = filectime($public_path_to_file);

        // Need for changing last modified time of publishable file
        sleep(1);
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class], true);
        }, $command);

        clearstatcache();

        $this->assertTrue($last_update < filectime($public_path_to_file));

        unlink($public_path_to_file);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function exception_on_invalid_provider($command)
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
