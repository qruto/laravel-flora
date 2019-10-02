<?php

namespace MadWeb\Initializer\Test;

use Illuminate\Support\Facades\Artisan;
use MadWeb\Initializer\Run;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class CallableRunnerCommandTest extends RunnerCommandsTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function callable($command)
    {
        $mock = Mockery::mock()->shouldReceive('someMethod')->once()->getMock();
        $this->declareCommands(function (Run $run) use ($mock) {
            $run->callable([
                $mock,
                'someMethod',
            ]);
        }, $command);
    }

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function callable_verbose($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->callable(function () {
                return 'Some string';
            });
        }, $command, true);

        self::assertStringContainsString("'Some string'", Artisan::output());
    }
}
