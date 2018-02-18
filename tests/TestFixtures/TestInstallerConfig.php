<?php

namespace MadWeb\Initializer\Test\TestFixtures;

use MadWeb\Initializer\Contracts\Runner;

class TestInstallerConfig
{
    public function testing(Runner $run)
    {
        return $run;
    }
}
