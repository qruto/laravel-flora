<?php

namespace Qruto\Formula;

use Exception;

class UndefinedScriptException extends Exception
{
    public static function forCustom(string $name): self
    {
        return new self("No custom script registered with name [$name], please register it using Run::newScript().");
    }

    public static function forEnvironment(string $environment): self
    {
        // TODO: separate
        return new self("No instructions found for [$environment] environment");
    }
}
