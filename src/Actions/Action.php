<?php

namespace Qruto\Initializer\Actions;

use Exception;
use Illuminate\Console\View\Components\Factory;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Throwable;

abstract class Action
{
    private bool $failed = false;

    protected Throwable $exception;

    public function __construct(protected Factory $outputComponents)
    {
    }

    public function __invoke(): bool
    {
        $this->outputComponents->task($this->title(), function () {
            try {
                return $this->run();
            } catch (Exception $e) {
                if ($e instanceof ProcessSignaledException) {
                    return false;
                }

                $this->exception = $e;

                $this->failed = true;

                return false;
            }
        });

        return ! $this->failed;
    }

    public function failed(): bool
    {
        return $this->failed;
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    abstract public function title(): string;

    abstract public function run(): bool;
}
