<?php

namespace Qruto\Flora\Console\Commands;

use Qruto\Flora\Enums\FloraType;

class UpdateCommand extends FloraCommand
{
    protected FloraType $type = FloraType::Update;

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
