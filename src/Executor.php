<?php

namespace ZFort\AppInstaller;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Symfony\Component\Process\Process;
use Illuminate\Contracts\Bus\Dispatcher;
use Symfony\Component\Process\ProcessBuilder;
use ZFort\AppInstaller\Contracts\Executor as ExecutorContract;

class Executor implements ExecutorContract
{
    protected $installCommand;

    public function __construct(Command $installCommand)
    {
        $this->installCommand = $installCommand;
    }

    public function exec(array $commands)
    {
        foreach ($commands as $command) {
            $this->{$command['type']}($command['command'], $command['arguments']);
        }
    }

    public function artisan(string $command, array $arguments = [])
    {
        $this->installCommand->call($command, $arguments);
    }

    public function external(string $command, array $arguments = [])
    {
        $Builder = new ProcessBuilder(array_merge([$command], $arguments));
        $Builder->setTimeout(null);

        $Process = $Builder->getProcess();
        if ((bool) @proc_open(
            'echo 1 >/dev/null',
            [
                ['file', '/dev/tty', 'r'],
                ['file', '/dev/tty', 'w'],
                ['file', '/dev/tty', 'w'],
            ],
            $pipes
        )) {
            $Process->setTty(true);
        } elseif (Process::isPtySupported()) {
            $Process->setPty(true);
        }
        $Process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->installCommand->error($buffer);
            } else {
                $this->installCommand->line($buffer);
            }
        });
    }

    public function callable(callable $function, array $arguments = [])
    {
        call_user_func($function, ...$arguments);

        is_callable($function, false, $name);
        $this->installCommand->info("Callable: $name called");
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
        $message = 'Job "' . get_class($job) . '" has been processed';

        $message .= is_string($result) ? '. Result: ' . $result : '';

        $this->installCommand->info($message);
    }
}
