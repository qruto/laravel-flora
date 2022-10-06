<?php

namespace Qruto\Initializer;

use Illuminate\Console\Application;
use Illuminate\Console\Concerns\InteractsWithSignals;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory;
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
    use InteractsWithSignals;

    private array $exceptions = [];

    private bool $doneWithErrors = false;

    protected Factory $outputComponents;

    protected ?Action $latestAction;

    protected bool $shouldClearLatestFail = false;

    public function __construct(protected Application $application, protected OutputStyle $output)
    {
        $this->outputComponents = new Factory($this->output);
    }

    protected function getApplication(): Application
    {
        return $this->application;
    }

    public function exceptions(): array
    {
        return $this->exceptions;
    }

    private function run(Action $action): static
    {
        $this->latestAction = $action;

        if ($this->shouldClearLatestFail) {
            $this->output->write("\x1B[1A");
            $this->output->write("\x1B[2K");

            $this->shouldClearLatestFail = false;
        }

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

    public function runLatestAction(): void
    {
        $this->run($this->latestAction);

        $this->shouldClearLatestFail = true;
    }

    public function doneWithErrors(): bool
    {
        return $this->doneWithErrors;
    }

    public function command(string $command, array $parameters = []): RunnerContract
    {
        return $this->run(new Artisan($this->outputComponents, $this->application, $command, $parameters));
    }

    public function publish($providers, bool $force = false): RunnerContract
    {
        return $this->run(new Publish($this->outputComponents, $providers, $force));
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
        return $this->run(new Process($this->outputComponents, $command, $parameters));
    }

    public function call(callable $callback, array $parameters = []): RunnerContract
    {
        return $this->run(new Callback($this->outputComponents, $callback, $parameters));
    }

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): RunnerContract
    {
        return $this->run(new Job($this->outputComponents, $job, $queue, $connection));
    }
}
