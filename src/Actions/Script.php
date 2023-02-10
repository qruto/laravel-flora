<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\View\Components\Factory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Traits\ReflectsClosures;
use Qruto\Initializer\Run;
use Symfony\Component\Console\Output\OutputInterface;
use function Termwind\render;
use function Termwind\terminal;

class Script extends Action
{
    use ReflectsClosures;

    public static string $label = 'script';

    protected string $color = '#f97316';

    public function __construct(
        protected Container $container,
        protected Run $runner,
        protected string $name,
        protected $callback,
        protected array $arguments,
        protected OutputInterface $output,
    ) {
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

        if ($this->runner->internal->doneWithFailures() && ! empty($this->runner->internal->exceptions())) {
            $this->exception = $this->runner->internal->exceptions()[0]['e'];
        }

        return $this->failed();
    }

    public function run(int $labelWidth = 0): bool
    {
        $this->container->call($this->callback, ['run' => $this->runner, ...$this->arguments]);

        $this->runner->internal->start($labelWidth);

        if ($this->output->isVerbose()) {
            $this->writeDotsLine();
        }

        return ! $this->runner->internal->doneWithFailures();
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
