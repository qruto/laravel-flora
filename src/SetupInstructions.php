<?php

namespace Qruto\Flora;

use function base_path;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use Qruto\Flora\Actions\Artisan;
use Qruto\Flora\Actions\Process;
use Qruto\Flora\Actions\Script;
use Qruto\Flora\Console\Commands\PackageInstruction;
use Qruto\Flora\Contracts\ChainVault;
use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\NullOutput;

class SetupInstructions
{
    use PackageInstruction;

    private readonly string $customFilePath;

    private string $defaultFilePath = __DIR__.'/setup.php';

    public function __construct(
        private readonly Container $container,
        private readonly ChainVault $vault
    ) {
        $this->customFilePath = base_path('routes/setup.php');
    }

    public function customExists(): bool
    {
        return file_exists($this->customFilePath);
    }

    public function load(): void
    {
        if ($this->customExists()) {
            $this->loadCustom();

            return;
        }

        $this->loadDefault();
    }

    public function loadDefault(): void
    {
        require $this->defaultFilePath;
    }

    private function loadCustom(): void
    {
        require $this->customFilePath;
    }

    protected function generateSetupCode(
        FloraType $type,
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

    public function publish(Application $application): void
    {
        $this->loadDefault();

        $code = Str::before(file_get_contents($this->defaultFilePath), 'App::install');

        $run = $this->makeRunner($application);

        foreach (FloraType::cases() as $type) {
            foreach (Environment::cases() as $env) {
                $this->vault->get($type)->get($env->value)($run);

                $this->instructPackages($type, $env->value, $run);

                $code .= $this->generateSetupCode($type, $env, $run).PHP_EOL.PHP_EOL;

                $run = $this->makeRunner($application);
            }
        }

        file_put_contents($this->customFilePath, Str::beforeLast($code, PHP_EOL));
    }

    private function makeRunner(Application $application): mixed
    {
        return $this->container->make(Run::class, [
            'application' => $application,
            'output' => new NullOutput(),
        ]);
    }
}
