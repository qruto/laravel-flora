<?php

namespace Qruto\Formula\Console\Commands;

use Qruto\Formula\Enums\FormulaType;

class InstallCommand extends FormulaCommand
{
    protected FormulaType $type = FormulaType::Install;

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
