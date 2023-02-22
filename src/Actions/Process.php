<?php

namespace Qruto\Power\Actions;

use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process as ProcessFacade;
use RuntimeException;

class Process extends Action
{
    /** Label for custom process */
    public static string $label = 'exec';

    /** Show custom process label in blue color */
    protected string $color = 'blue';

    public function __construct(
        protected string $command,
        protected array $parameters = []
    ) {
    }

    /** Get custom process name */
    public function name(): string
    {
        $argumentsString = implode(' ', $this->parameters);

        return "$this->command".($argumentsString !== '' ? ' '.$argumentsString : '');
    }

    /** Handle custom process */
    public function run(): bool
    {
        $result = $this->runProcess();

        if ($result->successful()) {
            return true;
        }

        throw new RuntimeException(trim($result->errorOutput()), $result->exitCode());
    }

    /** Run custom process */
    private function runProcess(): ProcessResult
    {
        if ($this->parameters === []) {
            return ProcessFacade::forever()->run($this->command);
        }

        return ProcessFacade::forever()->run(array_merge([$this->command], $this->parameters));
    }

    /** Get custom process command name */
    public function getCommand(): string
    {
        return $this->command;
    }
}
