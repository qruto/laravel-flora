<?php

namespace Qruto\Initializer\Contracts;

/**
 * @method self local(callable $callback)
 * @method self production(callable $callback)
 */
interface ChainContract
{
    public function run(string $environment);
}
