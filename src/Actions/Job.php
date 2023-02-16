<?php

namespace Qruto\Formula\Actions;

use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;

class Job extends Action
{
    public static string $label = 'job';

    protected string $color = 'magenta';

    public function __construct(
        protected object|string $job,
        protected ?string $queue = null,
        protected ?string $connection = null
    ) {
    }

    public function name(): string
    {
        return is_string($this->job) ? $this->job : $this->job::class;
    }

    public function run(): bool
    {
        $dispatcher = Container::getInstance()->make(Dispatcher::class);

        $job = is_string($this->job) ? Container::getInstance()->make($this->job) : $this->job;

        $dispatcher->dispatchNow($job);

        return true;
    }
}
