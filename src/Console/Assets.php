<?php

namespace Qruto\Formula\Console;

use Illuminate\Console\View\Components\Factory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Events\VendorTagPublished;
use Qruto\Formula\AssetPublishException;
use Qruto\Formula\Enums\FormulaType;
use function throw_if;

class Assets
{
    public function __construct(protected Dispatcher $events, protected Kernel $artisan, protected Repository $config)
    {
    }

    public function publish(FormulaType $type, Factory $components, bool $verbose = false): bool
    {
        $assets = $this->config['formula.assets'];

        if ($assets === []) {
            return true;
        }

        if ($type === FormulaType::Install && ! $this->config['formula.always_publish']) {
            return true;
        }

        foreach (resolve('formula.packages') as $package) {
            if ($package->exists() && $tag = $package->instruction()->assetsTag) {
                $assets[] = $tag;
            }
        }

        $assetsString = '';

        foreach ($assets as $key => $value) {
            if (is_string($key)) {
                $assetsString .= $key.': '.(is_array($value) ? implode(', ', $value) : $value);
            } else {
                $assetsString .= $value;
            }

            $assetsString .= ', ';
        }

        $assetsString = rtrim($assetsString, ', ');

        $tags = [];

        $publishCallbacks = [];

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
                $publishCallbacks[] = fn () => $this->artisan->call('vendor:publish', $parameters + ['--force' => true, '--no-interaction' => true]) === 0;
            }
        }

        if ($tags !== []) {
            $publishCallbacks[] = fn () => $this->artisan->call('vendor:publish', ['--tag' => $tags, '--force' => true]) === 0;
        }

        $title = '<fg=yellow>Publishing assets</>';

        $publishCallback = fn () => collect($publishCallbacks)
            ->map(fn (callable $callback) => $callback())
            ->each(fn ($value) => throw_if(! $value, AssetPublishException::class));

        $result = true;

        if ($verbose) {
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
        }

        try {
            if ($verbose) {
                $components->twoColumnDetail("$title <fg=gray>$assetsString</>");
                $publishCallback();
            } else {
                $components->task($title, $publishCallback);
            }
        } catch (AssetPublishException) {
            $result = false;
        }

        return $result;
    }
}
