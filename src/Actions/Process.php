<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Contracts\Console\Process\ProcessResult;
use RuntimeException;
use Illuminate\Support\Facades\Process as ProcessFacade;

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

        return "<fg=blue;options=bold>exec   </> $this->command $argumentsString";
    }

    public function run(): bool
    {
        $result = $this->runProcess();

        if ($result->successful()) {
            return true;
        }

        throw new RuntimeException(trim($result->errorOutput()), $result->exitCode() ?? 1);
    }

    private function runProcess(): ProcessResult
    {
        if ($this->parameters === []) {
            return ProcessFacade::forever()->run($this->command);
        }

        return ProcessFacade::forever()->run(array_merge([$this->command], $this->parameters));
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
