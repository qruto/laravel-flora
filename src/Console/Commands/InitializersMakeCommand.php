<?php

namespace MadWeb\Initializer\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class InitializersMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:initializers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates application update and installation config classes.';

    protected $type = 'Install config';

    protected $nameInput = 'Install';

    protected $stubFileName = 'install-config.stub';

    public function handle()
    {
        $installCreateResult = parent::handle();

        $this->nameInput = 'Update';
        $this->type = 'Update config';
        $this->stubFileName = 'update-config.stub';

        $updateCreateResult = parent::handle();

        return $installCreateResult and $updateCreateResult;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../../stubs/'.$this->stubFileName;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return $this->nameInput;
    }
}
