<?php

namespace Qruto\Initializer;

use Illuminate\Console\Application;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory;
use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Actions\Callback;
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

    protected Factory $outputComponents;

    public function __construct(protected Application $application, OutputStyle $output)
    {
        $this->outputComponents = new Factory($output);

        $this->internal = new RunInternal($this->application, $output);
    }

    public function command(string $command, array $parameters = []): RunnerContract
    {
        $this->internal->push(new Artisan($this->outputComponents, $this->application, $command, $parameters));

        return $this;
    }

    public function publish($providers, bool $force = false): RunnerContract
    {
        $this->internal->push(new Publish($this->outputComponents, $providers, $force));

        return $this;
    }

    public function publishForce($providers): RunnerContract
    {
        return $this->publish($providers, true);
    }

    public function publishTag($tag, bool $force = false): RunnerContract
    {
        return $this->run(new PublishTag($this->outputComponents, $tag, $force));
    }

    public function publishTagForce($tag): RunnerContract
    {
        return $this->publishTag($tag, true);
    }

    public function exec(string $command, array $parameters = []): RunnerContract
    {
        $this->internal->push(new Process($this->outputComponents, $command, $parameters));

        return $this;
    }

    public function call(callable $callback, array $parameters = []): RunnerContract
    {
        $this->internal->push(new Callback($this->outputComponents, $callback, $parameters));

        return $this;
    }

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): RunnerContract
    {
        $this->internal->push(new Job($this->outputComponents, $job, $queue, $connection));

        return $this;
    }
}
