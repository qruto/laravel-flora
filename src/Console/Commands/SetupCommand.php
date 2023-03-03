<?php

namespace Qruto\Power\Console\Commands;

use Illuminate\Console\Command;
use Qruto\Power\SetupInstructions;

class SetupCommand extends Command
{
    use PackageInstruction;

    public $signature = 'power:setup {--force : Overwrite existing build instructions}';

    public $description = 'Publish setup instructions.';

    public function handle(SetupInstructions $instructions): int
    {
        $forced = $this->option('force');

        if ($instructions->customExists() && ! $forced) {
            $this->components->warn('Setup instructions already exist. Use <fg=cyan>--force</> to overwrite.');
        } elseif (($app = $this->getApplication()) !== null) {
            $instructions->publish($app);
            $this->components->info('Setup instructions published to [routes/setup.php]');
        }

        $this->call('vendor:publish', ['--tag' => 'power-config', '--force' => $forced]);

        return self::SUCCESS;
    }
}
