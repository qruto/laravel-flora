<?php

namespace Qruto\Formula\Actions;

use Illuminate\Console\View\Components\Factory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Traits\ReflectsClosures;
use Qruto\Formula\Run;
use function Termwind\terminal;

class Script extends Action
{
    use ReflectsClosures;

    public static string $label = 'script';

    protected string $color = '#f97316';

    public function __construct(
        protected Container $container,
        protected Run $run,
        protected string $name,
        protected           $callback,
        protected array $arguments,
    ) {
        $this->container->call($this->callback, ['run' => $this->run, ...$this->arguments]);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function __invoke(Factory $outputComponents, int $labelWidth = 0): bool
    {
        $callback = fn (): bool => $this->successful = $this->run($labelWidth);

        $title = $this->title($labelWidth);

        if ($this->output->isVerbose()) {
            $this->output->write("  $title ");
            $this->writeDotsLine($labelWidth);

            $callback();
        } else {
            $outputComponents->task($title, $callback);
        }

        if ($this->run->internal->terminated()) {
            $this->output->write("\x1B[1A");
            $this->output->write("\x1B[2K");
        }

        if ($this->run->internal->doneWithFailures() && ! empty($this->run->internal->exceptions())) {
            $this->exception = $this->run->internal->exceptions()[0]['e'];
        }

        return $this->failed();
    }

    public function run(int $labelWidth = 0): bool
    {
        $this->run->internal->breakOnTerminate()->start($labelWidth);

        if ($this->output->isVerbose()) {
            $this->writeDotsLine();
        }

        return ! $this->run->internal->doneWithFailures();
    }

    private function writeDotsLine(int $offset = 0): void
    {
        $width = min(terminal()->width(), 150);
        $dots = max($width - $offset - 11, 0);

        if ($offset === 0) {
            $this->output->write('  ');
            $dots += 7;
        }

        $this->output->writeLn(str_repeat('<fg=gray>.</>', $dots), false);
    }
}
