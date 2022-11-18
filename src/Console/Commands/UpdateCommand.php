<?php

namespace Qruto\Initializer\Console\Commands;

use Qruto\Initializer\Enums\InitializerType;

class UpdateCommand extends AbstractInitializeCommand
{
    protected InitializerType $type = InitializerType::Update;

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
