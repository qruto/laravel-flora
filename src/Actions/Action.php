<?php

namespace Qruto\Initializer\Actions;

use Exception;
use Illuminate\Console\View\Components\Factory;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Throwable;

abstract class Action
{
    protected ?Throwable $exception = null;

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

    abstract public function title(): string;

    abstract public function run(): bool;
}
