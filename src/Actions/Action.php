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

    protected static string $label;

    protected string $color = 'white';

    protected string $description;

    protected bool $successful = false;

    public function __invoke(Factory $outputComponents): bool
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

        $outputComponents->task($this->title(), $callback);

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

    private function spaces(string $title): string
    {
        $actions = [
            Artisan::class,
            Callback::class,
            Job::class,
            Process::class,
            Script::class,
        ];

        $maxTitle = '';

        foreach ($actions as $action) {
            $maxTitle = strlen((string) $maxTitle) < strlen((string) $action::$label) ? $action::$label : $maxTitle;
        }

        return str_repeat(' ', strlen((string) $maxTitle) - strlen($title));
    }

    public function title(): string
    {
        $name = $this->name();
        $title = static::$label;
        $description = $this->description();

        $title = "<fg={$this->color};options=bold>$title".$this->spaces($title).' </>'.$name;

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
