<?php

namespace MadWeb\Initializer;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Symfony\Component\Process\Process;
use Illuminate\Contracts\Bus\Dispatcher;
use MadWeb\Initializer\Contracts\Executor as ExecutorContract;

class Executor implements ExecutorContract
{
    protected $artisanCommand;

    public function __construct(Command $artisanCommand)
    {
        $this->artisanCommand = $artisanCommand;
    }

    public function exec(array $commands)
    {
        foreach ($commands as $command) {
            $this->{$command['type']}($command['command'], $command['arguments']);
        }
    }

    public function artisan(string $command, array $arguments = [])
    {
        $this->artisanCommand->call($command, $arguments);
    }

    public function external(string $command, array $arguments = [])
    {
        $Process = new Process(empty($arguments) ? $command : array_merge([$command], $arguments));
        $Process->setTimeout(null);

        if (Process::isTtySupported()) {
            $Process->setTty(true);
        } elseif (Process::isPtySupported()) {
            $Process->setPty(true);
        }
        $Process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->artisanCommand->error($buffer);
            } else {
                $this->artisanCommand->line($buffer);
            }
        });
    }

    public function callable(callable $function, array $arguments = [])
    {
        call_user_func($function, ...$arguments);

        is_callable($function, false, $name);
        $this->artisanCommand->info("Callable: $name called");
    }

    public function dispatch($job)
    {
        $this->printJob($job, Container::getInstance()->make(Dispatcher::class)->dispatch($job));
    }

    public function dispatchNow($job)
    {
        $this->printJob($job, Container::getInstance()->make(Dispatcher::class)->dispatchNow($job));
    }

    protected function printJob($job, $result)
    {
        $message = 'Job "'.get_class($job).'" has been processed';

        $message .= is_string($result) ? '. Result: '.$result : '';

        $this->artisanCommand->info($message);
    }
}
