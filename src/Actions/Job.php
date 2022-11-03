<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\View\Components\Factory;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;

class Job extends Action
{
    public function __construct(
        Factory $outputComponents,
        protected object|string $job,
        protected ?string $queue = null,
        protected ?string $connection = null
    ) {
        parent::__construct($outputComponents);
    }

    public function title(): string
    {
        return '<fg=yellow>Dispatching</> '.$this->job::class;
    }

    public function run(): bool
    {
        $dispatcher = Container::getInstance()->make(Dispatcher::class);

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

        return true;
    }
}
