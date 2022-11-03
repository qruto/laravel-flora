<?php

namespace Qruto\Initializer\Console\Commands;

use Qruto\Initializer\Enums\InitializerType;

class InstallCommand extends AbstractInitializeCommand
{
    protected InitializerType $type = InitializerType::Install;

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

    protected function title(): string
    {
        return 'install';
    }
}
