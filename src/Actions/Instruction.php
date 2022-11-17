<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\View\Components\Factory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Traits\ReflectsClosures;
use Qruto\Initializer\Contracts\Runner;
use function Termwind\render;

class Instruction extends Action
{
    use ReflectsClosures;

    public function __construct(
        protected Container $container,
        protected Runner $runner,
        protected string $name,
        protected $callback,
        protected array $arguments,
        protected bool $detailed = false,
    ) {
    }

    public function title(): string
    {
        $color = $this->detailed ? 'green' : 'yellow';

        return "<fg=$color>Performing</> $this->name";
    }

    public function __invoke(Factory $outputComponents): bool
    {
        $callback = fn (): bool => $this->run();

        if ($this->detailed) {
            $outputComponents->twoColumnDetail($this->title());
            $callback();
        } else {
            $outputComponents->task($this->title(), $callback);
        }

        if ($this->runner->internal->doneWithErrors()) {
            $this->exception = $this->runner->internal->exceptions()[0]['e'];
        }

        return $this->failed();
    }

    public function run(): bool
    {
        $this->container->call($this->callback, ['run' => $this->runner, ...$this->arguments]);

        $this->runner->internal->start();

        if ($this->detailed) {
            render('
                <div class="flex mx-2 max-w-150">
                    <span class="flex-1 content-repeat-[.] text-gray"></span>
                </div>
            ');
        }

        return ! $this->runner->internal->doneWithErrors();
    }
}
