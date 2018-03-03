<?php

namespace MadWeb\Initializer\Test;

use MadWeb\Initializer\Run;
use Illuminate\Support\Facades\Artisan;

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

        $this->assertEquals(Artisan::output(), $comment."\n");
    }
}
