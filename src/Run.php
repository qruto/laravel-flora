<?php

namespace Qruto\Initializer;

use Illuminate\Console\Application;
use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Actions\Callback;
use Qruto\Initializer\Actions\Instruction;
use Qruto\Initializer\Actions\Job;
use Qruto\Initializer\Actions\Process;
use Qruto\Initializer\Contracts\Runner as RunnerContract;
use Symfony\Component\Console\Output\OutputInterface;

class Run implements RunnerContract
{
    /**
     * @internal
     */
    public RunInternal $internal;

    public function __construct(protected Application $application, protected OutputInterface $output)
    {
        // TODO: up and down
        $this->internal = new RunInternal($this->application, $output);
    }

    public static function newInstruction(string $name, callable $callback)
    {
        RunInternal::instruction($name, $callback);
    }

    public function instruction(string $name, array $arguments = [])
    {
        if (! RunInternal::hasInstruction($name)) {
            throw UndefinedInstructionException::forCustom($name);
        }

        $this->internal->push(new Instruction(
            $this->application->getLaravel(),
            $this->internal->newRunner(),
            $name,
            $this->internal->getInstruction($name),
            $arguments,
            $this->output->isVerbose(),
        ));

        return $this;
    }

    public function command(string $command, array $parameters = []): RunnerContract
    {
        $this->internal->push(new Artisan($this->application, $command, $parameters, $this->output->isVerbose()));

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
