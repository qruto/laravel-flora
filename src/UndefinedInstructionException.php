<?php

namespace Qruto\Initializer;

use Exception;

class UndefinedInstructionException extends Exception
{
    public function __construct(string $environment)
    {
        parent::__construct("No instructions found for [$environment] environment");
    }
}
