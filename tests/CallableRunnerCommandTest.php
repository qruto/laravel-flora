<?php

namespace MadWeb\Initializer\Test;

use Mockery;
use MadWeb\Initializer\Run;

class CallableRunnerCommandTest extends RunnerCommandsTestCase
{
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
