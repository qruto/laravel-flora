<?php

namespace Qruto\Flora\Console;

use Closure;
use Illuminate\Console\View\Components\Factory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\Collection;
use Qruto\Flora\AssetPublishException;

use function throw_if;

class Assets
{
    private static string $title = '<fg=yellow>Assets publishing</>';

    public function __construct(protected Dispatcher $events, protected Kernel $artisan, protected Repository $config)
    {
    }

    public function publish(Factory $components, bool $verbose = false): bool
    {
        $assets = $this->config['flora.assets'];

        foreach (resolve('flora.packages') as $package) {
            if (! $package->exists()) {
                continue;
            }
            if (! ($tag = $package->instruction()->assetsTag)) {
                continue;
            }
            $assets[] = $tag;
        }

        if ($assets === []) {
            return true;
        }

        try {
            if ($verbose) {
                $this->runVerbose($assets, $components);
            } else {
                $this->run($assets, $components);
            }
        } catch (AssetPublishException) {
            return false;
        }

        return true;
    }

    private function run(array $assets, Factory $components): void
    {
        $components->task(self::$title, $this->makePublishCallback($assets));
    }

    private function runVerbose(array $assets, Factory $components): void
    {
        $this->events->listen(static function (VendorTagPublished $event) use ($components): void {
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
        $forced = $this->config['flora.force_publish'];

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

            if (! ($parameters['--provider'] !== '' && $parameters['--provider'] !== '0')) {
                continue;
            }

            $publishCallbacks[] = fn (): bool => $this->artisan->call('vendor:publish', $parameters + ['--force' => $forced]) === 0;
        }

        if ($tags !== []) {
            $publishCallbacks[] = fn (): bool => $this->artisan->call('vendor:publish', ['--tag' => $tags, '--force' => $forced]) === 0;
        }

        return static fn (): Collection => collect($publishCallbacks)
            ->map(static fn (callable $callback): bool => $callback())
            ->each(static fn ($value): bool => throw_if(! $value, AssetPublishException::class));
    }
}
