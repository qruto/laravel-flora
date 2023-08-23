<?php

namespace Qruto\Flora\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Process;
use Qruto\Flora\Actions\ActionTerminatedException;
use function Qruto\Flora\any;
use Qruto\Flora\AssetsVersion;
use function Qruto\Flora\clearOutputLineAbove;
use Qruto\Flora\Console\Assets;
use Qruto\Flora\Console\StopSetupException;
use Qruto\Flora\Contracts\Chain;
use Qruto\Flora\Contracts\ChainVault;
use Qruto\Flora\Discovers\PackageDiscoverException;
use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;
use Qruto\Flora\Run;
use Qruto\Flora\SetupInstructions;
use Qruto\Flora\UndefinedInstructionException;
use Qruto\Flora\UndefinedScriptException;

abstract class FloraCommand extends Command
{
    use PackageInstruction;

    /**
     * The type of build.
     */
    protected FloraType $type;

    /**
     * Execute the action.
     */
    public function handle(
        Container $container,
        AssetsVersion $assetsVersion,
        ChainVault $vault,
        ExceptionHandler $exceptionHandler,
        Schedule $schedule,
        SetupInstructions $instructions,
    ): int {
        $run = $container->make(Run::class, [
            'application' => $this->getApplication(),
            'output' => $this->getOutput(),
        ]);

        $this->trap([SIGTERM, SIGINT], function ($signal) use ($run) {
            if ($this->components->confirm('Sure you want to stop')) {
                $this->components->warn(ucfirst($this->title()).' aborted without completion');

                throw new StopSetupException();
            }

            $run->internal->rerunLatestAction();

            throw new ActionTerminatedException($run->internal->getLatestAction(), $signal);
        });

        try {
            return $this->perform($vault, $container, $run, $assetsVersion, $exceptionHandler, $schedule, $instructions);
        } catch (StopSetupException) {
            clearOutputLineAbove($this->output);

            return self::FAILURE;
        }
    }

    /**
     * Returns vault of instructions for current command type.
     */
    protected function getFlora(ChainVault $vault): Chain
    {
        return $vault->get($this->type);
    }

    /**
     * Returns command action title.
     */
    abstract protected function title(): string;

    /**
     * Ask user to show errors.
     */
    private function askToShowErrors(array $exceptions, ExceptionHandler $exceptionHandler): void
    {
        if ($exceptions === []) {
            return;
        }

        if (! $this->components->confirm('Show errors?')) {
            return;
        }

        foreach ($exceptions as $exception) {
            $this->components->twoColumnDetail($exception['title'], '<fg=red;options=bold>FAIL</>');

            $exceptionHandler->renderForConsole($this->getOutput(), $exception['e']);
            $exceptionHandler->report($exception['e']);

            $this->output->newLine();
            $this->output->newLine();
        }
    }

    private function discoverPackages(): bool
    {
        if ($this->output->isVerbose()) {
            return $this->call('package:discover') === 0;
        }

        try {
            /** @throws PackageDiscoverException */
            $this->components->task(
                'Packages discovery',
                function () {
                    if ($this->callSilent('package:discover') !== 0) {
                        throw new PackageDiscoverException();
                    }
                }
            );

            return true;
        } catch (PackageDiscoverException) {
            return false;
        }
    }

    private function publishAssets(AssetsVersion $assetsVersion): bool
    {
        $success = true;

        if ($assetsVersion->outdated()) {
            $success = $this->laravel[Assets::class]->publish($this->components, $this->output->isVerbose());
        } else {
            $this->components->twoColumnDetail('<fg=green>No assets for publishing</>');
        }

        if ($this->output->isVerbose()) {
            $this->output->newLine();
        }

        return $success;
    }

    private function registerScheduler(string $env, Schedule $schedule): void
    {
        if (any(
            fn (): bool => $this->type !== FloraType::Install,
            fn (): bool => Environment::Production->value !== $env,
            fn (): bool => $schedule->events() === [],
        )) {
            return;
        }

        $task = sprintf('* * * * * cd %s && php artisan schedule:run >> /dev/null 2>&1', base_path());

        $result = Process::run('crontab -l');

        if (str_contains($result->output(), $task)) {
            $this->components->warn('Cron entry for task scheduling already exists');

            return;
        }

        if (! $this->components->confirm('Add a cron entry for task scheduling?')) {
            return;
        }

        Process::run(sprintf(
            '(crontab -l 2>/dev/null; echo "%s") | crontab -',
            $task
        ));

        $this->components->info("Entry was added [{$task}]");
    }

    private function perform(
        ChainVault $vault,
        Container $container,
        Run $run,
        AssetsVersion $assetsVersion,
        ExceptionHandler $exceptionHandler,
        Schedule $schedule,
        SetupInstructions $instructions,
    ): int {
        $instructions->load();

        $autoInstruction = !$instructions->customExists();

        $flora = $this->getFlora($vault);

        $env = $this->getLaravel()->environment();

        try {
            $container->call($flora->get($env), ['run' => $run]);
        } catch (UndefinedScriptException|UndefinedInstructionException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($autoInstruction) {
            $this->instructPackages($this->type, $env, $run);
        }

        $this->components->alert(sprintf('Application %s', $this->title()));

        $this->output->newLine();

        $packagesDiscovered = $this->discoverPackages();

        if ($this->output->isVerbose()) {
            $this->components->info('Running actions');
        } else {
            $this->output->newLine();
        }

        $run->internal->start();

        $this->output->newLine();

        $assetsPublished = $this->publishAssets($assetsVersion);

        $this->output->newLine();

        $assetsVersion->stampUpdate();

        if ($run->internal->doneWithFailures() || ! $assetsPublished || ! $packagesDiscovered) {
            $this->askToShowErrors($run->internal->exceptions(), $exceptionHandler);

            $this->components->error(ucfirst($this->title()).' occur errors. Run with <fg=cyan>-v</> flag to see more details');

            return self::FAILURE;
        }

        $this->registerScheduler($env, $schedule);

        $this->components->info(ucfirst($this->title()).' done!');

        return self::SUCCESS;
    }
}
