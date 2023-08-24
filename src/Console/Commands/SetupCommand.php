<?php

namespace Qruto\Flora\Console\Commands;

use Illuminate\Console\Command;
use Qruto\Flora\SetupInstructions;

class SetupCommand extends Command
{
    use PackageInstruction;

    public $signature = 'flora:setup {--force : Overwrite existing build instructions}
        {--script : Add [@php artisan update] to [post-autoload-dump] scripts}';

    public $description = 'Publish setup instructions.';

    public function handle(SetupInstructions $instructions): int
    {
        $forced = $this->option('force');
        $script = $this->option('script');

        if ($script) {
            $this->addToDumpAutoloadScripts();

            return self::SUCCESS;
        }

        if ($instructions->customExists() && ! $forced) {
            $this->components->warn('Setup instructions already exist. Use <fg=cyan>--force</> to overwrite.');
        } elseif (($app = $this->getApplication()) instanceof \Symfony\Component\Console\Application) {
            $instructions->publish($app);
            $this->components->info('Setup instructions published to [routes/setup.php]');
        }

        $this->call('vendor:publish', ['--tag' => 'flora-config', '--force' => $forced]);

        $this->addToDumpAutoloadScripts();

        return self::SUCCESS;
    }

    private function addToDumpAutoloadScripts(): void
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true, 512, JSON_THROW_ON_ERROR);
        $scripts = [];

        if (! isset($composer['scripts'])) {
            $composer['scripts'] = [];
        }

        if (! isset($composer['scripts']['post-autoload-dump'])) {
            $composer['scripts']['post-autoload-dump'] = [];
        }

        foreach ($composer['scripts']['post-autoload-dump'] as $script) {
            $scripts[] = $script === '@php artisan package:discover --ansi' ? '@php artisan update' : $script;
        }

        if (! in_array('@php artisan update', $scripts)) {
            $scripts[] = '@php artisan update';
        }

        $composer['scripts']['post-autoload-dump'] = $scripts;

        if (isset($composer['scripts']['post-update-cmd'])) {
            $scripts = [];

            foreach ($composer['scripts']['post-update-cmd'] as $script) {
                if ($script === '@php artisan vendor:publish --tag=laravel-assets --ansi --force') {
                    continue;
                }

                $scripts[] = $script;
            }

            if ($scripts === []) {
                unset($composer['scripts']['post-update-cmd']);
            } else {
                $composer['scripts']['post-update-cmd'] = $scripts;
            }
        }

        file_put_contents(base_path('composer.json'), json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);

        $this->components->info('[@php artisan update] added to <fg=cyan>post-autoload-dump</> scripts');
    }
}
