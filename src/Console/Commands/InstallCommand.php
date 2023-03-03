<?php

namespace Qruto\Flora\Console\Commands;

use Qruto\Flora\Enums\FloraType;

class InstallCommand extends FloraCommand
{
    protected FloraType $type = FloraType::Install;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

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
