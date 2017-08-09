<?php

namespace ZFort\AppInstaller\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class InstallerMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:installer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create application installation config file';

    protected $type = 'InstallerConfig';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/installer-config.stub';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return 'InstallerConfig';
    }
}
