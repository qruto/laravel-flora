<?php

namespace MadWeb\Initializer\Test;

use Illuminate\Support\Facades\Artisan;
use MadWeb\Initializer\Run;
use Illuminate\Support\Facades\Bus;
use MadWeb\Initializer\Test\TestFixtures\TestJob;
use MadWeb\Initializer\Test\TestFixtures\TestJobWithReturn;

class DispatchRunnerCommandTest extends RunnerCommandsTestCase
{
    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function dispatch_job($command)
    {
        $test_value = 'test value';

        Bus::fake();

        $this->declareCommands(function (Run $run) use ($test_value) {
            $run->dispatch(new TestJob($test_value));
        }, $command);

        Bus::assertDispatched(TestJob::class, function (TestJob $job) use ($test_value) {
            return $job->testValue === $test_value;
        });
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function dispatch_job_now($command)
    {
        $test_value = 'test value';

        Bus::fake();

        $this->declareCommands(function (Run $run) use ($test_value) {
            $run->dispatchNow(new TestJob($test_value));
        }, $command);

        Bus::assertDispatched(TestJob::class, function (TestJob $job) use ($test_value) {
            return $job->testValue === $test_value;
        });
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function dispatch_job_twice($command)
    {
        $test_value = 'test value';

        Bus::fake();

        $this->declareCommands(function (Run $run) use ($test_value) {
            $run
                ->dispatch(new TestJob($test_value))
                ->dispatchNow(new TestJob($test_value));
        }, $command);

        Bus::assertDispatched(TestJob::class, 2);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function dispatch_job_verbose($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatchNow(new TestJobWithReturn);
        }, $command, true);

        self::assertStringContainsString('Some string', Artisan::output());
    }
}
