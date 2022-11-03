<?php

namespace Qruto\Initializer;

use Illuminate\Console\Application;
use Illuminate\Console\Concerns\InteractsWithSignals;
use Illuminate\Console\OutputStyle;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Support\Traits\ReflectsClosures;
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
    use ReflectsClosures;

    private array $collection = [];

    private array $exceptions = [];

    protected Factory $outputComponents;

    protected ?Action $latestAction = null;

    protected bool $shouldClearLatestFail = false;

    public function __construct(protected Application $application, protected OutputStyle $output)
    {
        $this->outputComponents = new Factory($this->output);
    }

    protected function getApplication(): Application
    {
        return $this->application;
    }

    public function start(): void
    {
        foreach ($this->collection as $action) {
            $this->run($action);
        }
    }

    public function runLatestAction(): void
    {
        $this->run($this->latestAction);

        $this->shouldClearLatestFail = true;
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function filter(callable $callback): self
    {
        $actionType = $this->firstClosureParameterType($callback);

        $this->collection = collect($this->collection)->filter(
            fn ($action) => ! collect($this->collection)
                ->filter(static fn ($action) => $action instanceof $actionType)
                ->filter($callback)
                ->contains(static fn ($value) => $action === $value)
        )->values()->all();

        return $this;
    }

    public function exceptions(): array
    {
        return $this->exceptions;
    }

    public function doneWithErrors(): bool
    {
        return ! empty($this->exceptions());
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
            $this->exceptions[] = [
                'title' => $action->title(),
                'e' => $action->getException(),
            ];
        }

        return $this;
    }

    public function command(string $command, array $parameters = []): RunnerContract
    {
        $this->collection[] = new Artisan($this->outputComponents, $this->application, $command, $parameters);

        return $this;
    }

    public function publish($providers, bool $force = false): RunnerContract
    {
        $this->collection[] = new Publish($this->outputComponents, $providers, $force);

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
        $this->collection[] = new Process($this->outputComponents, $command, $parameters);

        return $this;
    }

    public function call(callable $callback, array $parameters = []): RunnerContract
    {
        $this->collection[] = new Callback($this->outputComponents, $callback, $parameters);

        return $this;
    }

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): RunnerContract
    {
        return $this->run(new Job($this->outputComponents, $job, $queue, $connection));
    }
}
