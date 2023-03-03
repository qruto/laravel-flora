<?php

namespace Qruto\Flora;

use Illuminate\Console\Application;
use Qruto\Flora\Actions\Artisan;
use Qruto\Flora\Actions\Callback;
use Qruto\Flora\Actions\Job;
use Qruto\Flora\Actions\Notification;
use Qruto\Flora\Actions\Process;
use Qruto\Flora\Actions\Script;
use Symfony\Component\Console\Output\OutputInterface;

class Run
{
    /**
     * @internal
     */
    public RunInternal $internal;

    public function __construct(
            protected Application $application,
            protected OutputInterface $output,
    ) {
        // TODO: up and down
        $this->internal = new RunInternal($this->application, $output, $this);
    }

    public static function newScript(string $name, callable $callback)
    {
        RunInternal::script($name, $callback);
    }

    public function script(string $name, array $arguments = []): static
    {
        if (! RunInternal::hasScript($name)) {
            throw UndefinedScriptException::forCustom($name);
        }

        $this->internal->push(new Script(
            $this->application->getLaravel(),
            $this->internal->newRunner(),
            $name,
            $this->internal->getScript($name),
            $arguments,
        ));

        return $this;
    }

    public function command(string $command, array $parameters = []): static
    {
        $this->internal->push(new Artisan($this->application, $command, $parameters));

        return $this;
    }

    public function exec(string $command, array $parameters = []): static
    {
        $this->internal->push(new Process($command, $parameters));

        return $this;
    }

    public function call(callable $callback, array $parameters = [], ?string $name = null): static
    {
        $this->internal->push(new Callback($this->application->getLaravel(), $callback, $parameters, $name));

        return $this;
    }

    public function job(object|string $job, ?string $queue = null, ?string $connection = null): static
    {
        $this->internal->push(new Job($job, $queue, $connection));

        return $this;
    }

    public function notify(string $text, string $body, $icon = null): self
    {
        $this->internal->push(new Notification($this->application->getLaravel(), $text, $body, $icon));

        return $this;
    }
}
