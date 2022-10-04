<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;

class Job extends Action
{
    // TODO: compatible interface
    public function __construct(
        Command $artisanCommand,
        protected object|string $job,
        protected ?string $queue = null,
        protected ?string $connection = null
    ) {
        parent::__construct($artisanCommand);
    }

    public function title(): string
    {
        return '<fg=yellow>Dispatching</> '.get_class($this->job);
    }

    public function run(): bool
    {
        /** @var Dispatcher */
        $dispatcher = Container::getInstance()->make(Dispatcher::class);

        $result = null;
        $job = is_string($this->job) ? Container::getInstance()->make($this->job) : $this->job;

        if ($job instanceof ShouldQueue) {
            $dispatcher->dispatch(
                $job->onConnection($this->connection ?? $job->connection)
                    ->onQueue($this->queue ?? $job->queue)
            );
        } else {
            $dispatcher->dispatchNow($job);
        }

        //TODO: unique jobs

        $artisanCommand = $this->getInitializerCommand();

        if ($artisanCommand->getOutput()->isVerbose()) {
            $artisanCommand->getOutput()->newLine();
            $artisanCommand->info($result);
        }

        return ! (is_int($result) and $result > 0);
    }
}
