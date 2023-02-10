<?php

use Illuminate\Console\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\App;
use Qruto\Initializer\Contracts\ChainVault;
use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;
use Qruto\Initializer\Run;
use Qruto\Initializer\Tests\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

uses(TestCase::class)->in(__DIR__);

function chain(?callable $callback = null, $verbose = false): object
{
    if ($callback) {
        App::update('testing', $callback);
    }

    return new class($verbose)
    {
        public function __construct(protected bool $verbose)
        {
        }

        public function run($options = [])
        {
            if (! $this->verbose) {
                putenv('SHELL_VERBOSITY=0');
            }

            return test()->artisan('update', $this->verbose ? ['--verbose' => $this->verbose] + $options : $options);
        }
    };
}

function makeRunner(?OutputInterface $output = null): Run
{
    return new Run(new Application(app(), app()->make(Dispatcher::class), 'unknown'), $output ?? new BufferedOutput());
}

function actionNamesForEnvironment(InitializerType $type, Environment $env, ?Run $runner = null): array
{
    $runner = $runner ?? makeRunner();

    $vault = app(ChainVault::class);

    $vault->get($type)->get($env->value)($runner);

    return runnerActionNames($runner);
}

function runnerActionNames(Run $runner): array
{
    return collect($runner->internal->getCollection())
        ->map(fn ($action) => $action->name())
        ->toArray();
}
