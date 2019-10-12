<?php

namespace MadWeb\Initializer\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class MakeCronTask
{
    use Dispatchable, Queueable;

    /**
     * Execute the job.
     *
     * @return string
     */
    public function handle()
    {
        $base_path = base_path();
        $task = "* * * * * cd $base_path && php artisan schedule:run >> /dev/null 2>&1";
        exec('(crontab -l 2>/dev/null; echo "'.$task.'") | crontab -');

        return 'Base cron task for scheduling work created. Task: '.$task;
    }
}
