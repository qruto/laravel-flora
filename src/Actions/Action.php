<?php

namespace Qruto\Formula\Actions;

use Exception;
use Illuminate\Console\View\Components\Factory;
use function strlen;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

abstract class Action
{
    protected ?Throwable $exception = null;

    public static string $label;

    protected string $color = 'white';

    protected string $description;

    protected bool $terminated = false;

    protected bool $successful = false;

    protected bool $silent = false;

    protected OutputInterface $output;

    public function __invoke(Factory $outputComponents, int $labelWidth): bool
    {
        $callback = function (): bool {
            try {
                return $this->successful = $this->run();
            } catch (Exception $e) {
                if ($e instanceof ActionTerminatedException) {
                    $this->terminated = true;

                    return $this->successful = true;
                }

                $this->exception = $e;

                return $this->successful = false;
            }
        };

        if ($this->silent) {
            return $callback();
        }

        $outputComponents->task($this->title($labelWidth), $callback);

        if ($this->terminated) {
            $this->output->write("\x1B[1A");
            $this->output->write("\x1B[2K");
        }

        return $this->failed();
    }

    public function failed(): bool
    {
        return ! $this->successful;
    }

    public function terminated(): bool
    {
        return $this->terminated;
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    public function withOutput(OutputInterface $output): static
    {
        $this->output = $output;

        return $this;
    }

    private function spaces(string $title, int $width): string
    {
        if ($width === 0) {
            return '';
        }

        return str_repeat(' ', $width - strlen($title));
    }

    public function title(int $width = 0): string
    {
        $name = $this->name();
        $title = static::$label;
        $description = $this->description();

        $spaces = $this->spaces($title, $width);
        $title = "<fg={$this->color};options=bold>$title</>$spaces $name";

        if ($description !== '' && $description !== '0') {
            $title .= " <fg=gray>$description</>";
        }

        return $title;
    }

    abstract public function name(): string;

    protected function description(): string
    {
        return '';
    }

    abstract public function run(): bool;

    public function isSilent(): bool
    {
        return $this->silent;
    }
}
