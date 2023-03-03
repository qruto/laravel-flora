<?php

namespace Qruto\Flora\Actions;

use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;

class Job extends Action
{
    /** Job action label */
    public static string $label = 'job';

    /** Show job action label in magenta color */
    protected string $color = 'magenta';

    public function __construct(
        protected object|string $job,
        protected ?string $queue = null,
        protected ?string $connection = null
    ) {
    }

    /** Get job class name */
    public function name(): string
    {
        return is_string($this->job) ? $this->job : $this->job::class;
    }

    /** Dispatch job immediately */
    public function run(): bool
    {
        $dispatcher = Container::getInstance()->make(Dispatcher::class);

        $job = is_string($this->job) ? Container::getInstance()->make($this->job) : $this->job;

        $dispatcher->dispatchNow($job);

        return true;
    }
}
