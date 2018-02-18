<?php

namespace MadWeb\Initializer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use MadWeb\Initializer\Contracts\Executor as ExecutorContract;

class InstallCommand extends Command
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
    protected $description = 'Install the application based on the current environment';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Container $container)
    {
        $Executor = $container->makeWith(ExecutorContract::class, ['installCommand' => $this]);

        $Config = $container->make('config');
        $env = $Config->get($Config->get('initializer.env_config_key'));

        $Executor->exec($container->call([
            $container->make('project.installer'),
            $this->option('root') ? $env.'Root' : $env,
        ])->getCommands());
    }
}
