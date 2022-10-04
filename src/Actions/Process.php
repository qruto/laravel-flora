<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\Process as ExternalProcess;

class Process extends Action
{
    public function __construct(
        Command $initializerCommand,
        protected string $command,
        protected array $parameters = []
    ) {
        parent::__construct($initializerCommand);
    }

    public function title(): string
    {
        $argString = implode(' ', $this->parameters);

        return "<fg=yellow>Processing</> $this->command $argString";
    }

    public function run(): bool
    {
        $Process = $this->createProcess();
        $Process->setTimeout(null);

        $isVerbose = $this->initializerCommand->getOutput()->isVerbose();

        if ($isVerbose) {
            $this->initializerCommand->getOutput()->newLine();
            if (ExternalProcess::isTtySupported()) {
                $Process->setTty(true);
            } elseif (ExternalProcess::isPtySupported()) {
                $Process->setPty(true);
            }
        }

        $Process->run($isVerbose ? function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->initializerCommand->error($buffer);
            } else {
                $this->initializerCommand->line($buffer);
            }
        } : null);

        $error = $Process->getErrorOutput();
        $exitCode = $Process->getExitCode();

        if ($error and $exitCode > 0) {
            throw new RuntimeException(trim($error));
        }

        return ! $exitCode;
    }

    private function createProcess(): ExternalProcess
    {
        if (empty($this->parameters)) {
            return ExternalProcess::fromShellCommandline($this->command);
        }

        return new ExternalProcess(array_merge([$this->command], $this->parameters));
    }
}
