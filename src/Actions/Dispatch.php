<?php

namespace MadWeb\Initializer\Actions;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;

class Dispatch extends Action
{
    protected const LOADING_TEXT = 'dispatching...';

    private $job;

    private $runNow;

    public function __construct(Command $artisanCommand, $job, bool $runNow = false)
    {
        parent::__construct($artisanCommand);

        $this->job = $job;
        $this->runNow = $runNow;
    }

    public function title(): string
    {
        return '<comment>Dispatch job:</comment> '.get_class($this->job);
    }

    public function run(): bool
    {
        $result = null;

        if ($this->runNow) {
            $result = Container::getInstance()->make(Dispatcher::class)->dispatchNow($this->job);
        } else {
            $result = Container::getInstance()->make(Dispatcher::class)->dispatch($this->job);
        }

        $artisanCommand = $this->getArtisanCommnad();

        if ($artisanCommand->getOutput()->isVerbose()) {
            $artisanCommand->getOutput()->newLine();
            $artisanCommand->info($result);
        }

        return ! (is_int($result) and $result > 0);
    }
}
