<?php

namespace Qruto\Flora\Console\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Encryption\MissingAppKeyException;
use Qruto\Flora\AssetsVersion;
use Qruto\Flora\Contracts\ChainVault;
use Qruto\Flora\Enums\FloraType;
use Qruto\Flora\SetupInstructions;

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

    public function handle(Container $container, AssetsVersion $assetsVersion, ChainVault $vault, ExceptionHandler $exceptionHandler, Schedule $schedule, SetupInstructions $instructions): int
    {
        if (config('app.key') === '') {
            $this->components->warn((new MissingAppKeyException())->getMessage());

            if ($this->components->confirm('Run the installation first?', true)) {
                return $this->call('install', $this->arguments());
            }

            return self::SUCCESS;
        }

        return parent::handle($container, $assetsVersion, $vault, $exceptionHandler, $schedule, $instructions);
    }

    protected function title(): string
    {
        return 'update';
    }
}
