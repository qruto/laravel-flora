<?php

namespace MadWeb\Initializer\ExecutorActions;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;

class Dispatch
{
    private $artisanCommand;

    private $job;

    private $runNow;

    public function __construct(Command $artisanCommand, $job, bool $runNow = false)
    {
        $this->artisanCommand = $artisanCommand;
        $this->job = $job;
        $this->runNow = $runNow;
    }

    private function title()
    {
        return '<comment>Dispatching job:</comment> '.get_class($this->job);
    }

    public function __invoke(): bool
    {
        return $this->artisanCommand->task($this->title(), function () {
            $result = null;

            if ($this->runNow) {
                $result = Container::getInstance()->make(Dispatcher::class)->dispatchNow($this->job);
            } else {
                $result = Container::getInstance()->make(Dispatcher::class)->dispatch($this->job);
            }

            if ($this->artisanCommand->getOutput()->isVerbose()) {
                $this->artisanCommand->getOutput()->newLine();
                $this->artisanCommand->info($result);
            }
        });
    }
}
