<?php

namespace Qruto\Initializer;

use Exception;

class UndefinedInstructionException extends Exception
{
    public static function forCustom(string $name)
    {
        return new static("No custom instruction registered with name [$name], please register it using Run::newInstruction().");
    }

    public static function forEnvironment(string $environment): static
    {
        return new static("No instructions found for [$environment] environment");
    }
}
