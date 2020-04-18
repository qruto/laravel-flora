<?php

namespace MadWeb\Initializer\Test;

use Illuminate\Support\Facades\Artisan;
use MadWeb\Initializer\Run;

class ArtisanRunnerCommandTest extends RunnerCommandsTestCase
{
    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function console_command($command)
    {
        $comment = 'Some comment';

        Artisan::command('some:command', function () use ($comment) {
            $this->comment($comment);
        });

        $this->declareCommands(function (Run $run) {
            $run->artisan('some:command');
        }, $command);

        self::assertStringContainsString('Run artisan command: some:command (): ✔', Artisan::output());
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function console_command_verbose($command)
    {
        $comment = 'Some comment';

        Artisan::command('some:command', function () use ($comment) {
            $this->comment($comment);
        });

        $this->declareCommands(function (Run $run) {
            $run->artisan('some:command');
        }, $command, true);

        self::assertStringContainsString($comment, Artisan::output());
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function error_command($command)
    {
        config(['database.connections.testbench.database' => 'invalid_database']);

        $this->declareCommands(function (Run $run) {
            $run->artisan('migrate');
        }, $command);

        $this->assertErrorAppeared(
            'Database (invalid_database) does not exist',
            \Illuminate\Database\QueryException::class
        );
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function error_without_message($command)
    {
        $comment = 'Some comment';

        Artisan::command('some:command', function () use ($comment) {
            $this->comment($comment);

            return 1;
        });

        $this->declareCommands(function (Run $run) {
            $run->artisan('some:command');
        }, $command, true);

        $this->assertErrorAppeared("done with errors.\nYou could run command with -v flag to see more details");
    }
}
