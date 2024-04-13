<?php

use Illuminate\Console\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\App;
use Qruto\Flora\Contracts\ChainVault;
use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;
use Qruto\Flora\Run;
use Qruto\Flora\Tests\TestCase;
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

function actionNamesForEnvironment(FloraType $type, Environment $env, ?Run $run = null): array
{
    $run = $run ?? makeRunner();

    $vault = app(ChainVault::class);

    $vault->get($type)->get($env->value)($run);

    return runnerActionNames($run);
}

function runnerActionNames(Run $run): array
{
    return collect($run->internal->getCollection())
        ->map(fn ($action) => $action->name())
        ->toArray();
}
