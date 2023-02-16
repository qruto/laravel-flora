<?php

namespace Qruto\Formula\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use Qruto\Formula\Actions\Artisan;
use Qruto\Formula\Actions\Process;
use Qruto\Formula\Actions\Script;
use Qruto\Formula\Contracts\ChainVault;
use Qruto\Formula\Enums\Environment;
use Qruto\Formula\Enums\FormulaType;
use Qruto\Formula\Run;

class PublishCommand extends Command
{
    use PackageDiscover;

    public $signature = 'formula:publish';

    public $description = 'Publish setup instructions.';

    public function handle(Container $container, ChainVault $vault): int
    {
        require __DIR__.'/../../build.php';

        $code = Str::before(file_get_contents(__DIR__.'/../../build.php'), 'App::install');

        $run = $this->makeRunner($container);

        foreach (FormulaType::cases() as $type) {
            foreach (Environment::cases() as $env) {
                $vault->get($type)->get($env->value)($run);

                $this->discoverPackages($type, $env->value, $run);

                $code .= $this->generateFormulaCode($type, $env, $run).PHP_EOL.PHP_EOL;

                $run = $this->makeRunner($container);
            }
        }

        file_put_contents(base_path('routes/build.php'), Str::beforeLast($code, PHP_EOL));

        $this->components->info('Initialization instructions published to [routes/build.php]');

        return self::SUCCESS;
    }

    private function makeRunner(Container $container): mixed
    {
        return $container->make(Run::class, [
            'application' => $this->getApplication(),
            'output' => $this->getOutput(),
        ]);
    }

    protected function generateFormulaCode(
        FormulaType $type,
        Environment $environment,
        Run $run
    ): string {
        $code = sprintf("App::%s('%s', fn (Run \$run) => \$run", $type->value, $environment->value).PHP_EOL;
        $collection = $run->internal->getCollection();

        foreach ($collection as $item) {
            if ($item instanceof Artisan) {
                $name = $item->name();
                $parameters = $item->getParameters();

                $code .= "    ->command('$name'".($parameters === []
                    ? ')'
                    : ', '.str(var_export($parameters, true))->replace(PHP_EOL, '')->replace('array (  ', '[')->replace(',)', ']').')');
            } elseif ($item instanceof Process) {
                $name = $item->name();

                $code .= "    ->exec('$name')";
            } elseif ($item instanceof Script) {
                $name = $item->name();

                $code .= "    ->script('$name')";
            }

            $code .= PHP_EOL;
        }

        return $code.');';
    }
}
