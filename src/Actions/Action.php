<?php

namespace Qruto\Initializer\Actions;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Factory;
use Throwable;

abstract class Action
{
    private $failed = false;

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
