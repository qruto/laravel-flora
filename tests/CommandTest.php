<?php

use Qruto\Initializer\UndefinedInstructionException;

it('throws exception when no instructions found for current test environment',
    function () {
        chain()
            ->run()
            ->assertFailed()
            ->expectsOutputToContain(UndefinedInstructionException::forEnvironment('testing')->getMessage());
    }
);
