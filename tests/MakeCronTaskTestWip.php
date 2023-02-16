<?php

namespace Qruto\Formula\Tests;

use Qruto\Formula\Jobs\MakeCronTask;
use Qruto\Formula\Run;

class MakeCronTaskTestWip extends RunnerCommandsTestCase
{
    /**
     * @test
     *
     * @group cron-task
     *
     * @dataProvider initCommandsSet
     */
    public function dispatch_job($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatch(new MakeCronTask);
        }, $command);

        $base_path = base_path();
        $task = "* * * * * cd $base_path && php artisan schedule:run >> /dev/null 2>&1";

        $this->assertStringContainsString($task, exec('crontab -l'));

        exec('crontab -l | grep -v \''.$task.'\' | crontab -');
        if (empty(exec('crontab -l'))) {
            exec('crontab -r');
        }
    }
}
