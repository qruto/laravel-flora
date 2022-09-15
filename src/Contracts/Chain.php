<?php

namespace Qruto\Initializer\Contracts;

/**
 * @method self local(callable $callback)
 * @method self production(callable $callback)
 */
interface Chain
{
    public function getForEnvironment(string $env): callable;
}
