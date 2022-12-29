<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;

class Job extends Action
{
    public function __construct(
        protected object|string $job,
        protected ?string $queue = null,
        protected ?string $connection = null
    ) {
    }

    public function title(): string
    {
        return '<fg=yellow>Dispatching</> '.(is_string($this->job) ? $this->job : $this->job::class);
    }

    public function run(): bool
    {
        $dispatcher = Container::getInstance()->make(Dispatcher::class);

        $job = is_string($this->job) ? Container::getInstance()->make($this->job) : $this->job;

        try {
            $dispatcher->dispatch($job);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
