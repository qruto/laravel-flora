<?php

namespace MadWeb\Initializer\Test;

use MadWeb\Initializer\Run;
use MadWeb\Initializer\Jobs\MakeCronTask;

class MakeCronTaskTest extends RunnerCommandsTestCase
{
    /** @test */
    public function dispatch_job()
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatch(new MakeCronTask);
        });

        $base_path = base_path();
        $task = '* * * * * php '.$base_path.'/artisan schedule:run >> /dev/null 2>&1';

        $this->assertContains($task, exec('crontab -l'));

        exec('crontab -l | grep -v \''.$task.'\' | crontab -');
        if (empty(exec('crontab -l'))) {
            exec('crontab -r');
        }
    }
}
