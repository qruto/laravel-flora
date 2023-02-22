<?php

namespace Qruto\Power\Console\Commands;

use Qruto\Power\Enums\PowerType;

class UpdateCommand extends PowerCommand
{
    protected PowerType $type = PowerType::Update;

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
