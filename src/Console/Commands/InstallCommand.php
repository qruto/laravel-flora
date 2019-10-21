<?php

namespace MadWeb\Initializer\Console\Commands;

use Illuminate\Contracts\Container\Container;

class InstallCommand extends AbstractInitializeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install
                            {--root : Run commands which requires root privileges}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the application according to current environment';

    /**
     * Returns instance of Install class which defines initializing runner chain.
     *
     * {@inheritdoc}
     */
    protected function getInitializerInstance(Container $container)
    {
        return $container->make('app.installer');
    }

    protected function title(): string
    {
        return 'Application installation';
    }
}
