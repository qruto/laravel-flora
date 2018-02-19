<?php

namespace MadWeb\Initializer\Test;

use MadWeb\Initializer\Run;
use InvalidArgumentException;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderOne;
use MadWeb\Initializer\Test\TestFixtures\TestServiceProviderTwo;

class PublishRunnerCommandTest extends RunnerCommandsTestCase
{
    /** @test */
    public function publish_by_array()
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class]);
        });

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /** @test */
    public function publish_by_string()
    {
        $this->declareCommands(function (Run $run) {
            $run->publish(TestServiceProviderOne::class);
        });

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /** @test */
    public function publish_with_tag()
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class => 'public']);
        });

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileExists($public_path_to_file);

        unlink($public_path_to_file);
    }

    /** @test */
    public function publish_with_wrong_tag()
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([TestServiceProviderOne::class => 'wrong-tag']);
        });

        $public_path_to_file = public_path('test-publishable-one.txt');

        $this->assertFileNotExists($public_path_to_file);
    }

    /** @test */
    public function publish_multiple_providers()
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderOne::class,
                TestServiceProviderTwo::class,
            ]);
        });

        $public_path_to_file_one = public_path('test-publishable-one.txt');
        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_one);
        $this->assertFileExists($public_path_to_file_two);

        unlink($public_path_to_file_one);
        unlink($public_path_to_file_two);
    }

    /** @test */
    public function publish_multiple_providers_with_tags()
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderOne::class => 'public',
                TestServiceProviderTwo::class => 'public',
            ]);
        });

        $public_path_to_file_one = public_path('test-publishable-one.txt');
        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_one);
        $this->assertFileExists($public_path_to_file_two);

        unlink($public_path_to_file_one);
        unlink($public_path_to_file_two);
    }

    /** @test */
    public function publish_multiple_providers_with_one_wrong_tag()
    {
        $this->declareCommands(function (Run $run) {
            $run->publish([
                TestServiceProviderOne::class => 'public',
                TestServiceProviderTwo::class => 'wrong-tag',
            ]);
        });

        $public_path_to_file_one = public_path('test-publishable-one.txt');
        $public_path_to_file_two = public_path('test-publishable-two.txt');

        $this->assertFileExists($public_path_to_file_one);
        $this->assertFileNotExists($public_path_to_file_two);

        unlink($public_path_to_file_one);
    }

    /** @test */
    public function exception_on_invalid_argument()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->declareCommands(function (Run $run) {
            $run->publish(true);
        });
    }

    protected function getPackageProviders($app)
    {
        $providers = parent::getPackageProviders($app);

        array_push($providers, TestServiceProviderOne::class);
        array_push($providers, TestServiceProviderTwo::class);

        return $providers;
    }
}
