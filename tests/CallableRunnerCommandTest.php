<?php

namespace MadWeb\Initializer\Test;

use Mockery;
use MadWeb\Initializer\Run;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class CallableRunnerCommandTest extends RunnerCommandsTestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function callable()
    {
        $mock = Mockery::mock()->shouldReceive('someMethod')->once()->getMock();
        $this->declareCommands(function (Run $run) use ($mock) {
            $run->callable([
                $mock,
                'someMethod',
            ]);
        });
    }
}
