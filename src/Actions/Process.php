<?php

namespace Qruto\Initializer\Actions;

use RuntimeException;
use Symfony\Component\Process\Process as ExternalProcess;

class Process extends Action
{
    public function __construct(
        protected string $command,
        protected array $parameters = []
    ) {
    }

    public function title(): string
    {
        $argumentsString = implode(' ', $this->parameters);

        return "<fg=yellow>Processing</> $this->command $argumentsString";
    }

    public function run(): bool
    {
        $Process = $this->createProcess();
        $Process->setTimeout(null);

        $Process->run();

        $error = $Process->getErrorOutput();
        $exitCode = $Process->getExitCode();
        if ($error === '') {
            return ! $exitCode;
        }
        if ($error === '0') {
            return ! $exitCode;
        }
        if ($exitCode <= 0) {
            return ! $exitCode;
        }
        throw new RuntimeException(trim($error));
    }

    private function createProcess(): ExternalProcess
    {
        if ($this->parameters === []) {
            return ExternalProcess::fromShellCommandline($this->command);
        }

        return new ExternalProcess(array_merge([$this->command], $this->parameters));
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
