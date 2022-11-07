<?php

namespace Qruto\Initializer\Actions;

use Exception;
use Illuminate\Console\View\Components\Factory;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Throwable;

abstract class Action
{
    protected ?Throwable $exception = null;

    public function __invoke(Factory $outputComponents): bool
    {
        $outputComponents->task($this->title(), function (): bool {
            try {
                return $this->run();
            } catch (Exception $e) {
                if ($e instanceof ProcessSignaledException) {
                    return false;
                }

                $this->exception = $e;

                return false;
            }
        });

        return $this->failed();
    }

    public function failed(): bool
    {
        return ! is_null($this->exception);
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    abstract public function title(): string;

    abstract public function run(): bool;
}
