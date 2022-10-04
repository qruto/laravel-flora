<?php

namespace Qruto\Initializer\Actions;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Factory;
use Throwable;

abstract class Action
{
    protected Command $initializerCommand;

    private $failed = false;

    protected Throwable $exception;

    protected Factory $viewComponents;

    public function __construct(Command $initializerCommand)
    {
        $this->initializerCommand = $initializerCommand;

        $this->viewComponents = new Factory($this->initializerCommand->getOutput());
    }

    public function __invoke(): bool
    {
        $this->viewComponents->task($this->title(), function () {
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

    public function getInitializerCommand(): Command
    {
        return $this->initializerCommand;
    }
}
