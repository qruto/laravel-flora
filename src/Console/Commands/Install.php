<?php

namespace ZFort\AppInstaller\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Container $container
     * @return mixed
     */
    public function handle(Container $container)
    {
        $Config = $container->make('project.installer');
        $run = $container->call([$Config, config('app.env')]);
    }
}
