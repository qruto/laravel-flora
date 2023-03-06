<?php

namespace Qruto\Flora;

use Exception;

class UndefinedScriptException extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct("No custom script registered with name [$name], please register it using Run::newScript().");
    }
}
