<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
    ])
    ->withRules([InlineConstructorDefaultToPropertyRector::class])
    ->withPreparedSets(
        codeQuality: true,
        deadCode: true,
        earlyReturn: true,
    )
    ->withSets([
        LaravelSetList::LARAVEL_110,
    ])
    ->withPhpVersion(PhpVersion::PHP_81)
    ->withBootstrapFiles([__DIR__.'/vendor/larastan/larastan/bootstrap.php'])
    ->withPHPStanConfigs([__DIR__.'/phpstan.neon.rector.dist']);
