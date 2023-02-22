<?php

namespace Qruto\Power\Console\Commands;

use Qruto\Power\Enums\PowerType;

class InstallCommand extends PowerCommand
{
    protected PowerType $type = PowerType::Install;

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
