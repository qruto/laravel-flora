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
use Qruto\Initializer\Contracts\Runner;
use Qruto\Initializer\Contracts\Runner as RunnerContract;
use Qruto\Initializer\Discovers\HorizonDiscover;
use Qruto\Initializer\Discovers\IdeHelperDiscover;
use Qruto\Initializer\Discovers\TelescopeDiscover;
use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;

class Run implements RunnerContract
{
    use InteractsWithSignals;
    use ReflectsClosures;

    private array $collection = [];

    private array $exceptions = [];

    private bool $doneWithErrors = false;

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

    public function start()
    {
        foreach ($this->collection as $action) {
            $this->run($action);
        }
    }

    public function doneWithErrors(): bool
    {
        return $this->doneWithErrors;
    }

    public function filter(callable $callback): self
    {
        $actionType = $this->firstClosureParameterType($callback);

        $this->collection = collect($this->collection)->filter(
            fn ($action) => ! collect($this->collection)
                ->filter(fn ($action) => $action instanceof $actionType)
                ->filter($callback)
                ->contains(fn ($value) => $action === $value)
        )->values()->all();

        return $this;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    protected function packageDiscovers(InitializerType $type, string $environment, Runner $runner)
    {
        // TODO: build assets in production config value

        $discovers = [
            new HorizonDiscover(),
            new TelescopeDiscover(),
            new IdeHelperDiscover(),
        ];

        foreach ($discovers as $discover) {
            if ($discover->exists()) {
                $discover->instruction()
                    ->get($type, Environment::tryFrom($environment))($runner);
            }
        }
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
        return $this->run(new Callback($this->outputComponents, $callback, $parameters));
    }

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): RunnerContract
    {
        return $this->run(new Job($this->outputComponents, $job, $queue, $connection));
    }
}
