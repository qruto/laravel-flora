<?php

namespace MadWeb\Initializer\Contracts;

use Illuminate\Console\Command;

interface Executor
{
    public function __construct(Command $artisanCommand);

    public function exec(array $commands);
}
