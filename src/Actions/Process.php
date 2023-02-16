<?php

namespace Qruto\Formula\Actions;

use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process as ProcessFacade;
use RuntimeException;

class Process extends Action
{
    public static string $label = 'exec';

    protected string $color = 'blue';

    public function __construct(
        protected string $command,
        protected array $parameters = []
    ) {
    }

    public function name(): string
    {
        $argumentsString = implode(' ', $this->parameters);

        return "$this->command".($argumentsString !== '' ? ' '.$argumentsString : '');
    }

    public function run(): bool
    {
        $result = $this->runProcess();

        if ($result->successful()) {
            return true;
        }

        throw new RuntimeException(trim($result->errorOutput()), $result->exitCode());
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
