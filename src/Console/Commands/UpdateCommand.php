<?php

namespace Qruto\Initializer\Console\Commands;

use Illuminate\Contracts\Container\Container;
use Qruto\Initializer\Contracts\Builder;
use Qruto\Initializer\Contracts\Chain;
use Qruto\Initializer\Contracts\ChainVault;

class UpdateCommand extends AbstractInitializeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update
                            {--root : Run commands which requires root privileges}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the application according to current environment';

    /**
     * Returns instance of Update class which defines initializing runner chain.
     *
     * {@inheritdoc}
     */
    protected function getInitializer(ChainVault $vault): Chain
    {
        return $vault->getUpdate();
    }

    protected function title(): string
    {
        return 'update';
    }
}
