<?php

namespace Qruto\Power\Console;

use Closure;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Events\VendorTagPublished;
use Qruto\Power\AssetPublishException;
use Qruto\Power\Enums\PowerType;
use function throw_if;

class Assets
{
    private static string $title = '<fg=yellow>Publishing assets</>';

    public function __construct(protected Dispatcher $events, protected Kernel $artisan, protected Repository $config)
    {
    }

    public function publish(PowerType $type, Factory $components, bool $verbose = false): bool
    {
        $assets = $this->config['power.assets'];

        if ($assets === []) {
            return true;
        }

        foreach (resolve('power.packages') as $package) {
            if ($package->exists() && $tag = $package->instruction()->assetsTag) {
                $assets[] = $tag;
            }
        }

        $result = true;

        try {
            if ($verbose) {
                $this->runVerbose($assets, $components);
            } else {
                $this->run($assets, $components);
            }
        } catch (AssetPublishException) {
            $result = false;
        }

        return $result;
    }

    private function run(array $assets, Factory $components): void
    {
        $components->task(self::$title, $this->makePublishCallback($assets));
    }

    private function runVerbose(array $assets, Factory $components): void
    {
        $this->events->listen(function (VendorTagPublished $event) use ($components) {
            foreach ($event->paths as $from => $to) {
                $assetType = null;

                if (is_file($from)) {
                    $assetType = 'file';
                } elseif (is_dir($from)) {
                    $assetType = 'directory';
                }

                $assetType ? $components->task(sprintf(
                    'Copying %s [%s] to [%s]',
                    $assetType,
                    realpath($from),
                    realpath($to),
                )) : $components->error("Can't locate path: <{$from}>");
            }
        });

        $components->twoColumnDetail(
            sprintf('%s <fg=gray>%s</>', self::$title, $this->assetsString($assets))
        );

        $this->makePublishCallback($assets)();
    }

    private function assetsString(array $assets): string
    {
        $assetsString = '';

        foreach ($assets as $key => $value) {
            if (is_string($key)) {
                $assetsString .= $key.': '.(is_array($value) ? implode(', ', $value) : $value);
            } else {
                $assetsString .= $value;
            }

            $assetsString .= ', ';
        }

        return rtrim($assetsString, ', ');
    }

    private function makePublishCallback(array $assets): Closure
    {
        $tags = [];
        $publishCallbacks = [];
        $forced = $this->config['power.force_publish'];

        foreach ($assets as $key => $value) {
            $parameters = ['--provider' => '', '--tag' => []];

            if (is_string($key)) {
                $parameters['--provider'] = $key;
                $parameters['--tag'] = is_string($value) ? [$value] : $value;
            } elseif (class_exists($value)) {
                $parameters['--provider'] = $value;
                unset($parameters['--tag']);
            } else {
                $tags[] = $value;
            }

            if (! empty($parameters['--provider'])) {
                $publishCallbacks[] = fn () => $this->artisan->call('vendor:publish', $parameters + ['--force' => $forced, '--no-interaction' => true]) === 0;
            }
        }

        if ($tags !== []) {
            $publishCallbacks[] = fn () => $this->artisan->call('vendor:publish', ['--tag' => $tags, '--force' => $forced, '--no-interaction' => true]) === 0;
        }

        return fn () => collect($publishCallbacks)
            ->map(fn (callable $callback) => $callback())
            ->each(fn ($value) => throw_if(! $value, AssetPublishException::class));
    }
}
