<?php

namespace Qruto\Power;

use Illuminate\Console\Application;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Support\Traits\ReflectsClosures;
use Qruto\Power\Actions\Action;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class RunInternal
{
    use ReflectsClosures;

    /**
     * The registered string instruction.
     */
    protected static array $scripts = [];

    protected Factory $outputComponents;

    private array $collection = [];

    private array $exceptions = [];

    protected ?Action $latestAction = null;

    protected bool $breakOnTerminate = false;

    protected bool $terminated = false;

    protected bool $finishedWithFailures = false;

    public function __construct(
        protected Application $application,
        protected OutputInterface $output,
        protected Run $run
    ) {
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
    public static function script(string $name, callable $script): void
    {
        static::$scripts[$name] = $script;
    }

    public static function hasScript(string $name): bool
    {
        return isset(static::$scripts[$name]);
    }

    public function getScript(string $name)
    {
        return static::$scripts[$name];
    }

    public function start(int $labelWidth = 0): void
    {
        foreach ($this->collection as $action) {
            $this->run($action, $labelWidth);

            if ($this->breakOnTerminate && $action->terminated()) {
                $this->terminated = true;
                break;
            }
        }
    }

    public function rerunLatestAction(): void
    {
        $this->run($this->latestAction);
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function push(Action $action): void
    {
        $action->withOutput($this->output);

        $this->collection[] = $action;
    }

    public function replace(callable $callback, callable $prepare): self|bool
    {
        $actionType = $this->firstClosureParameterType($callback);

        $keys = collect($this->collection)->filter(
            fn ($action) => $action instanceof $actionType
        )->search($callback);

        if ($keys === false) {
            return false;
        }

        $replaceAction = is_int($keys) ? $keys : $keys[0];

        $firstPart = array_slice($this->collection, 0, $replaceAction);
        $secondPart = array_slice($this->collection, $replaceAction + 1);

        $this->collection = $firstPart;

        $prepare($this->run);

        $this->collection = array_merge($this->collection, $secondPart);

        return $this;
    }

    public function exceptions(): array
    {
        return $this->exceptions;
    }

    public function doneWithFailures(): bool
    {
        return $this->finishedWithFailures;
    }

    public function terminated(): bool
    {
        return $this->terminated;
    }

    public function breakOnTerminate(): self
    {
        $this->breakOnTerminate = true;

        return $this;
    }

    private function run(Action $action, int $labelWidth = 0): self
    {
        $this->latestAction = $action;

        $internalLabelWidth = collect($this->collection)
            ->map(fn (Action $action) => $action->isSilent() ? '' : $action::$label)
            ->reduce(fn ($carry, $label) => max($carry, strlen($label)));

        $action(
            $this->outputComponents,
            $labelWidth && $labelWidth > $internalLabelWidth ? $labelWidth : $internalLabelWidth
        );

        if ($action->failed()) {
            $this->finishedWithFailures = true;

            if (($e = $action->getException()) !== null) {
                $this->exceptions[] = [
                    'title' => $action->title(),
                    'e' => $e,
                ];
            }
        }

        return $this;
    }

    public function getLatestAction(): ?Action
    {
        return $this->latestAction;
    }
}
