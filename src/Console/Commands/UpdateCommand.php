<?php

namespace Qruto\Formula\Console\Commands;

use Qruto\Formula\Enums\FormulaType;

class UpdateCommand extends FormulaCommand
{
    protected FormulaType $type = FormulaType::Update;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the application according to current environment';

    protected function title(): string
    {
        return 'update';
    }
}
