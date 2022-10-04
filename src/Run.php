<?php

namespace Qruto\Initializer;

use Illuminate\Console\Command;
use Qruto\Initializer\Actions\Action;
use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Actions\Callback;
use Qruto\Initializer\Actions\Job;
use Qruto\Initializer\Actions\Process;
use Qruto\Initializer\Actions\Publish;
use Qruto\Initializer\Actions\PublishTag;
use Qruto\Initializer\Contracts\Runner as RunnerContract;

class Run implements RunnerContract
{
    protected $initializerCommand;

    private $exceptions = [];

    private $doneWithErrors = false;

    public function __construct(Command $initializerCommand)
    {
        $this->initializerCommand = $initializerCommand;
    }

    public function exceptions(): array
    {
        return $this->exceptions;
    }

    private function run(Action $action)
    {
        $action();

        if ($action->failed()) {
            if (! $this->doneWithErrors) {
                $this->doneWithErrors = true;
            }

            if ($exception = $action->getException()) {
                $this->exceptions[] = [
                    'title' => $action->title(),
                    'e' => $exception,
                ];
            }
        }

        return $this;
    }

    public function doneWithErrors(): bool
    {
        return $this->doneWithErrors;
    }

    public function command(string $command, array $parameters = []): RunnerContract
    {
        return $this->run(new Artisan($this->initializerCommand, $command, $parameters));
    }

    public function publish($providers, bool $force = false): RunnerContract
    {
        return $this->run(new Publish($this->initializerCommand, $providers, $force));
    }

    public function publishForce($providers): RunnerContract
    {
        return $this->publish($providers, true);
    }

    public function publishTag($tag, bool $force = false): RunnerContract
    {
        return $this->run(new PublishTag($this->initializerCommand, $tag, $force));
    }

    public function publishTagForce($tag): RunnerContract
    {
        return $this->publishTag($tag, true);
    }

    public function exec(string $command, array $parameters = []): RunnerContract
    {
        return $this->run(new Process($this->initializerCommand, $command, $parameters));
    }

    public function call(callable $callback, array $parameters = []): RunnerContract
    {
        return $this->run(new Callback($this->initializerCommand, $callback, $parameters));
    }

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): RunnerContract
    {
        return $this->run(new Job($this->initializerCommand, $job, $queue, $connection));
    }
}
