<?php

namespace ZFort\AppInstaller\Contracts;

use Illuminate\Console\Command;

interface Executor
{
    public function __construct(Command $installCommand);

    public function exec(array $commands);
}
