<?php

namespace MadWeb\Initializer\Test;

use MadWeb\Initializer\Run;
use Illuminate\Support\Facades\Artisan;

class ArtisanRunnerCommandTest extends RunnerCommandsTestCase
{
    /** @test */
    public function console_command()
    {
        $comment = 'Some comment';

        Artisan::command('some:command', function () use ($comment) {
            $this->comment($comment);
        });

        $this->declareCommands(function (Run $run) {
            $run->artisan('some:command');
        });

        $this->assertEquals(Artisan::output(), $comment."\n");
    }
}
