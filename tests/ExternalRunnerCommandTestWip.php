<?php

namespace Qruto\Initializer\Test;

use Qruto\Initializer\Run;

it('can run external command', function () {
//    App::update('testing', fn (Run $run) => $run->exec('php -v'));

//    $this->assertStringContainsString('PHP', $this->output->fetch());
});

//class ExternalRunnerCommandTest extends RunnerCommandsTestCase
//{
//    /**
//     * @test
//     * @dataProvider initCommandsSet
//     */
//    public function external_by_string($command)
//    {
//        $test_file_path = $this->app->basePath('test-external.txt');
//
//        $this->assertFileDoesNotExist($test_file_path);
//
//        $this->declareCommands(function (Run $run) use ($test_file_path) {
//            $run->external('echo "test output" > '.$test_file_path);
//        }, $command);
//
//        $this->assertFileExists($test_file_path);
//
//        unlink($test_file_path);
//    }
//
//    /**
//     * @test
//     * @dataProvider initCommandsSet
//     */
//    public function external_by_array($command)
//    {
//        $test_file_path = $this->app->basePath('test-external.txt');
//
//        $this->assertFileDoesNotExist($test_file_path);
//
//        $this->declareCommands(function (Run $run) use ($test_file_path) {
//            $run->external('touch', $test_file_path);
//        }, $command);
//
//        $this->assertFileExists($test_file_path);
//
//        unlink($test_file_path);
//    }
//
//    /**
//     * @test
//     * @dataProvider initCommandsSet
//     */
//    public function external_error($command)
//    {
//        $this->declareCommands(function (Run $run) {
//            $run->external('invalid-command');
//        }, $command);
//
//        $this->assertErrorAppeared('invalid-command', RuntimeException::class);
//    }
//}