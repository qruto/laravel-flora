<?php

namespace Qruto\Initializer\Actions;

use Exception;
use Illuminate\Console\View\Components\Factory;
use function strlen;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Throwable;

abstract class Action
{
    protected ?Throwable $exception = null;

    public static string $label;

    protected string $color = 'white';

    protected string $description;

    protected bool $successful = false;

    public function __invoke(Factory $outputComponents, int $labelWidth): bool
    {
        $callback = function (): bool {
            try {
                return $this->successful = $this->run();
            } catch (Exception $e) {
                if ($e instanceof ProcessSignaledException) {
                    return $this->successful = true;
                }

                $this->exception = $e;

                return $this->successful = false;
            }
        };

        $outputComponents->task($this->title($labelWidth), $callback);

        return $this->failed();
    }

    public function failed(): bool
    {
        return ! $this->successful;
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    private function spaces(string $title, int $width): string
    {
        if ($width === 0) {
            return '';
        }

        return str_repeat(" ", $width - strlen($title));
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

    abstract protected function name(): string;

    protected function description(): string
    {
        return '';
    }

    abstract public function run(): bool;
}
