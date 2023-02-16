<?php

namespace Qruto\Formula\Test;

use Illuminate\Support\Facades\Process;
use Qruto\Formula\Run;

it('can run process', function () {
    Process::fake(['php -v' => 'always latest PHP version']);

    chain(fn (Run $run) => $run->exec('php -v'))
        ->run()
        ->expectsOutputToContain('exec php -v')
        ->assertSuccessful();
});

it('show errors if process failed', function () {
    Process::fake(['php -v' => Process::result(errorOutput: 'PHP is not installed', exitCode: 1)]);

    chain(fn (Run $run) => $run->exec('php -v'))
        ->run()
        ->expectsOutputToContain('exec php -v')
        ->expectsConfirmation('Show errors?', 'yes')
        ->expectsOutputToContain('PHP is not installed')
        ->assertFailed();
});

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
