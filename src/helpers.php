<?php

namespace Qruto\Flora;

use Closure;
use Symfony\Component\Console\Output\OutputInterface;

/** @param  Closure  ...$conditions */
function any(...$conditions): bool
{
    foreach ($conditions as $condition) {
        if ($condition()) {
            return true;
        }
    }

    return false;
}

function clearOutputLineAbove(OutputInterface $output): void
{
    $output->write("\x1B[1A");
    $output->write("\x1B[2K");
}
