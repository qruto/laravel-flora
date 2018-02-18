<?php

namespace MadWeb\Initializer\Test;

use MadWeb\Initializer\Run;

class ExternalRunnerCommandTest extends RunnerCommandsTestCase
{
    /** @test */
    public function external_by_string()
    {
        $test_file_path = $this->app->basePath('test-external.txt');

        $this->assertFileNotExists($test_file_path);

        $this->declareCommands(function (Run $run) use ($test_file_path) {
            $run->external('echo "test output" > '.$test_file_path);
        });

        $this->assertFileExists($test_file_path);

        unlink($test_file_path);
    }

    /** @test */
    public function external_by_array()
    {
        $test_file_path = $this->app->basePath('test-external.txt');

        $this->assertFileNotExists($test_file_path);

        $this->declareCommands(function (Run $run) use ($test_file_path) {
            $run->external('touch', $test_file_path);
        });

        $this->assertFileExists($test_file_path);

        unlink($test_file_path);
    }
}
