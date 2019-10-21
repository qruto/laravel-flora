<?php

namespace MadWeb\Initializer\Console\Commands;

use Illuminate\Contracts\Container\Container;

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
    protected function getInitializerInstance(Container $container)
    {
        return $container->make('app.updater');
    }

    protected function title(): string
    {
        return 'Application update';
    }
}
