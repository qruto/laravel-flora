<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\View\Components\Factory;
use RuntimeException;
use Symfony\Component\Process\Process as ExternalProcess;

class Process extends Action
{
    public function __construct(
        Factory $outputComponents,
        protected string $command,
        protected array $parameters = []
    ) {
        parent::__construct($outputComponents);
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

        if ($error && $exitCode > 0) {
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

    public function getCommand(): string
    {
        return $this->command;
    }
}
