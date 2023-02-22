<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Illuminate\Support\Facades
{
    use Qruto\Power\Run;

    class App
    {
        /**
         * @param  callable(Run): Run  $callback
         */
        public static function install(string $environment, callable $callback): void
        {
        }

        /**
         * @param  callable(Run): Run  $callback
         */
        public static function update(string $environment, callable $callback): void
        {
        }
    }
}
