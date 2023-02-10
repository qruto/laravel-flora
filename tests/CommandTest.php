<?php

use Qruto\Initializer\UndefinedScriptException;

it('throws exception when no instructions found for current test environment',
    function () {
        chain()
            ->run()
            ->assertFailed()
            ->expectsOutputToContain(UndefinedScriptException::forEnvironment('testing')->getMessage());
    }
);
