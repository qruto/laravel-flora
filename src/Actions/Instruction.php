<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Application;
use Illuminate\Console\View\Components\Factory;
use Qruto\Initializer\Contracts\Runner;

class Instruction extends Action
{
    public function __construct(
        Factory $outputComponents,
        protected Runner $runner,
        protected string $name,
        protected $callback,
        protected array $arguments,
    ) {
        parent::__construct($outputComponents);
    }

    public function title(): string
    {
        return "<fg=magenta>Instruction $this->name performing</>";
    }

    public function run(): bool
    {

    }
}
