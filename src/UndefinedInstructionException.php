<?php

namespace Qruto\Initializer;

use Exception;

class UndefinedInstructionException extends Exception
{
    public static function forCustom(string $name): self
    {
        return new self("No custom instruction registered with name [$name], please register it using Run::newInstruction().");
    }

    public static function forEnvironment(string $environment): self
    {
        return new self("No instructions found for [$environment] environment");
    }
}
