<?php

namespace Qruto\Initializer;

use Illuminate\Console\Application;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Support\Traits\Macroable;
use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Actions\Callback;
use Qruto\Initializer\Actions\Instruction;
use Qruto\Initializer\Actions\Job;
use Qruto\Initializer\Actions\Process;
use Qruto\Initializer\Actions\Publish;
use Qruto\Initializer\Actions\PublishTag;
use Qruto\Initializer\Contracts\Runner as RunnerContract;

class Run implements RunnerContract
{
    /**
     * @internal
     */
    public RunInternal $internal;


    public function __construct(protected Application $application, OutputStyle $output)
    {
        $this->internal = new RunInternal($this->application, $output);
    }

    public static function instruction(string $name, callable $callback)
    {
        RunInternal::macro($name, $callback);
    }

    public function __call(string $name, array $arguments)
    {
        if (!RunInternal::hasMacro($name)) {
            return;
        }

        $this->internal->push(new Instruction(
            $this->internal->newRunner(),
            $name,
            $this->internal->$name(...),
            $arguments
        ));
    }

    public function command(string $command, array $parameters = []): RunnerContract
    {
        $this->internal->push(new Artisan($this->application, $command, $parameters));

        return $this;
    }

    public function exec(string $command, array $parameters = []): RunnerContract
    {
        $this->internal->push(new Process($command, $parameters));

        return $this;
    }

    public function call(callable $callback, array $parameters = []): RunnerContract
    {
        $this->internal->push(new Callback($callback, $parameters));

        return $this;
    }

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): RunnerContract
    {
        $this->internal->push(new Job($job, $queue, $connection));

        return $this;
    }
}
