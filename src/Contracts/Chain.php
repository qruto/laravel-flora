<?php

namespace Qruto\Initializer\Contracts;

/**
 * @method self local(callable $callback)
 * @method self production(callable $callback)
 */
interface Chain
{
    public function set(string $environment, callable $callback);

    public function get(string $environment);
}
