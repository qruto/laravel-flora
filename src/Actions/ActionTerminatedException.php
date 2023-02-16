<?php

namespace Qruto\Formula\Actions;

use Exception;

class ActionTerminatedException extends Exception
{
    public function __construct(protected Action $action, protected int $signal)
    {
        parent::__construct(sprintf('The action [%s] `%s` has been signaled with signal "%d".', $action::class, $action->name(), $signal));
    }
}
