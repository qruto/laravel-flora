<?php

namespace Qruto\Initializer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use Qruto\Initializer\Actions\Artisan;
use Qruto\Initializer\Actions\Process;
use Qruto\Initializer\Contracts\ChainVault;
use Qruto\Initializer\Contracts\Runner;
use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;

class PublishCommand extends Command
{
    use PackageDiscover;

    public $signature = 'initializer:publish';

    public $description = 'Publish initialization instructions.';

    public function handle(Container $container, ChainVault $vault): int
    {
        require __DIR__.'/../../build.php';

        $code = Str::before(file_get_contents(__DIR__.'/../../build.php'), 'App::install');

        $runner = $this->makeRunner($container);

        foreach (InitializerType::cases() as $type) {
            foreach (Environment::cases() as $env) {
                $vault->get($type)->get($env->value)($runner);

                $this->packageDiscovers($type, $env->value, $runner);

                $code .= $this->generateInitializerCode($type, $env, $runner).PHP_EOL.PHP_EOL;

                $runner = $this->makeRunner($container);
            }
        }

        file_put_contents(base_path('routes/build.php'), Str::beforeLast($code, PHP_EOL));

        $this->components->info('Initialization instructions published to [routes/build.php]');

        return self::SUCCESS;
    }

    private function makeRunner(Container $container): mixed
    {
        return $container->make(Runner::class, [
            'application' => $this->getApplication(),
            'output' => $this->getOutput(),
        ]);
    }

    protected function generateInitializerCode(
        InitializerType $type,
        Environment $environment,
        Runner $runner
    ): string {
        $code = sprintf("App::%s('%s', fn (Runner \$run) => \$run", $type->value, $environment->value).PHP_EOL;
        $collection = $runner->getCollection();

        foreach ($collection as $item) {
            if ($item instanceof Artisan) {
                $command = $item->getCommand();
                $parameters = $item->getParameters();

                $code .= "    ->command('$command'".(empty($parameters)
                    ? ')'
                    : ', '.str(var_export($parameters, true))->replace(PHP_EOL, '')->replace('array (  ', '[')->replace(',)', ']').')');
            } elseif ($item instanceof Process) {
                $command = $item->getCommand();

                $code .= "    ->exec('$command')";
            }

            $code .= PHP_EOL;
        }

        return $code . ');';
    }
}
