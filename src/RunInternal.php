<?php

namespace Qruto\Initializer;

use Illuminate\Console\Application;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Support\Traits\ReflectsClosures;
use Qruto\Initializer\Actions\Action;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class RunInternal
{
    use ReflectsClosures;

    /**
     * The registered string instruction.
     */
    protected static array $instructions = [];

    protected Factory $outputComponents;

    private array $collection = [];

    private array $exceptions = [];

    protected ?Action $latestAction = null;

    protected bool $shouldClearLatestFail = false;

    public function __construct(protected Application $application, protected OutputInterface $output)
    {
        $this->outputComponents = new Factory($output);
    }

    public function newRunner()
    {
        return $this->application->getLaravel()->make(Run::class, [
            'application' => $this->application,
            'output' => $this->output->isVerbose() ? $this->output : new NullOutput(),
        ]);
    }

    /**
     * Register a custom instruction.
     */
    public static function instruction(string $name, callable $instruction): void
    {
        static::$instructions[$name] = $instruction;
    }

    public static function hasInstruction(string $name): bool
    {
        return isset(static::$instructions[$name]);
    }

    public function getInstruction(string $name)
    {
        return static::$instructions[$name];
    }

    public function getApplication(): Application
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

    public function push(Action $action): void
    {
        $this->collection[] = $action;
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

    private function run(Action $action): self
    {
        $this->latestAction = $action;

        if ($this->shouldClearLatestFail) {
            $this->output->write("\x1B[1A");
            $this->output->write("\x1B[2K");

            $this->shouldClearLatestFail = false;
        }

        $action($this->outputComponents);

        if ($action->failed()) {
            $this->exceptions[] = [
                'title' => $action->title(),
                'e' => $action->getException(),
            ];
        }

        return $this;
    }
}
