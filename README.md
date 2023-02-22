<p align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://github.com/qruto/laravel-power/raw/HEAD/art/logo-dark.svg">
        <source media="(prefers-color-scheme: light)" srcset="https://github.com/qruto/laravel-power/raw/HEAD/art/logo-light.svg">
        <img alt="Laravel Wave Logo" src="https://github.com/qruto/laravel-power/raw/HEAD/art/logo-light.svg">
    </picture>
</p>
<p align="center">A convenient way to automate <strong>setup</strong> of your application.</p>
<p align="center">
    <a href="https://github.com/qruto/laravel-power/actions/workflows/tests.yml"><img src="https://github.com/qruto/laravel-power/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a>
    <a href="https://github.com/qruto/laravel-power/actions/workflows/styles.yml"><img src="https://github.com/qruto/laravel-power/actions/workflows/styles.yml/badge.svg" alt="Styles check"></a>
    <a href="https://github.com/qruto/laravel-power/actions/workflows/types.yml"><img src="https://github.com/qruto/laravel-power/actions/workflows/types.yml/badge.svg" alt="Types check"></a>
    <a href="https://github.com/qruto/laravel-power/actions/workflows/refactor.yml"><img src="https://github.com/qruto/laravel-power/actions/workflows/refactor.yml/badge.svg" alt="Refactor code"></a>
    <a href="https://packagist.org/packages/qruto/laravel-power"><img src="https://img.shields.io/packagist/dt/qruto/laravel-power" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/qruto/laravel-power"><img src="https://img.shields.io/packagist/v/qruto/laravel-power" alt="Latest Stable Version"></a>
</p>
<p align="center">
    <img width="650" alt="Laravel Power Demo" src="https://github.com/qruto/laravel-power/raw/HEAD/art/demo.png" />
</p>

## Introduction

Power allows you to bring Laravel application to live by one command.
Use default or define custom chain of actions required to **install** or **update** application.

Replace ~~**installation**~~ section in readme file with:

```bash
php artisan install
```

Refresh application state by:

```bash
php artisan update
```

- after `composer install/update`
- after `git pull/checkout/megre/...`
- in deploy script
- in CI/CD pipeline

it will take care of the rest of the work.

> 🧠🚀 Put knowledge of the setup instructions at the application level

## Installation

Via Composer:

``` bash
composer require qruto/laravel-power
```

## Usage

When you just fetch a fresh application:

```bash
php artisan install
```

For refresh application state:

```bash
php artisan update
```

ℹ️ Instruction depends on current application **environment**. Package has predefined actions suitable for most cases.

See detailed output in verbosity mode:

```bash
php artisan app:update -v
```

## Configuration

To customize Power instructions for each environment, you need to publish setup files.

```bash
php artisan power:setup
```

This command will create `routes/setup.php` file with predefined instructions for `local` and `production` environments.

```php
use Qruto\Power\Run;

App::install('local', fn (Run $run) => $run
    ->command('key:generate')
    ->command('migrate')
    ->command('storage:link')
    ->script('build')
);

App::install('production', fn (Run $run) => $run
    ->command('key:generate', ['--force' => true])
    ->command('migrate', ['--force' => true])
    ->command('storage:link')
    ->script('cache')
    ->script('build')
);

App::update('local', fn (Run $run) => $run
    ->command('migrate')
    ->command('cache:clear')
    ->script('build')
);

App::update('production', fn (Run $run) => $run
    ->script('cache')
    ->command('migrate', ['--force' => true])
    ->command('cache:clear')
    ->command('queue:restart')
    ->script('build')
);
```

Feel free to change it any way you need or add specific environment like `staging`.

<details>

<summary>`build` and `cache` script details</summary>

`build` script contains assets building commands:

```bash
npm install
npm run build
```

`cache` script provides general application caching:

```bash
php artisan route:cache
php artisan config:cache
php artisan event:cache
```
</details>

Configure assets publishing in `config/power.php`.

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Force Assets Publish
    |--------------------------------------------------------------------------
    |
    | Force publish assets on every installation or update. By default, assets
    | will always be force published, which would completely automate the
    | setup. Switch it to false if you want to manually publish assets.
    | For example if you prefer to commit them.
    */
    'force_publish' => true,

    /*
    |--------------------------------------------------------------------------
    | Publishable Assets
    |--------------------------------------------------------------------------
    |
    | List of assets that will be published during installation or update.
    | Most of required assets detects on the way. If you need specific
    | tag or provider, feel free to add it to the array.
    */
    'assets' => [
        'laravel-assets',
    ],
];
```

If you need to customize just assets publishing, you can publish only configuration file:

```bash
php artisan vendor:publish --tag=power-config
```

### Custom Scripts

Override or define custom script in service provider's `boot` method:

```php
Run::newScript('cache', fn (Run $run) => $run
    ->command('route:cache')
    ->command('config:cache')
    ->command('event:cache')
    ->command('view:cache')
);
```

### Available Actions

```php
$run
    ->command('command') // Run artisan command
    ->script('build') // Perform custom script
    ->exec('process') // Execute external process
    ->job(new JobClass) // Dispatch job
    ->call(fn () => makeSomething()) // Call callable function 
    ->notify('Done!') // Send notification
```

## Useful jobs

Laravel power provides some useful jobs to make setup of your application much easier.

### Create cron task for scheduling tasks

To enable [Laravel Scheduling](https://laravel.com/docs/6.x/scheduling) add dispatch `MakeCronTask` job to runner chain to create cron task for your application.

```php
$run
    ...
    ->dispatch(new \MadWeb\Power\Jobs\MakeCronTask)
```

This job will add

```txt
* * * * * cd /app-path && php artisan schedule:run >> /dev/null 2>&1
```

to crontab list.

## Combine With Composer Scripts

Updating the application is required after any dependencies change.
To automate this process add `update` command to your application
`composer.json` script `post-autoload-dump` section and remove
default `vendor:publish` command from `post-update-cmd` section.
Update command will take care of assets publishing.

```diff
"post-autoload-dump": [
    "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump", 
+     "@php artisan package:discover --ansi",
+     "@php artisan update"
-     "@php artisan package:discover --ansi"
],
- "post-update-cmd": [
-     "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
- ],
```

Now everything is up-to-date after each dependency change.

## Upgrading

Please see [UPGRADING](UPGRADING.md) for details.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CONDUCT](.github/CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email bro@qruto.to instead of using the issue tracker.

## Credits

Thanks [Nuno Maduro](https://github.com/nunomaduro) for [laravel-desktop-notifier](https://github.com/nunomaduro/laravel-desktop-notifier) package which brings desktop notifications to Laravel.

- [Qruto](https://github.com/qruto)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/qruto
[link-contributors]: ../../contributors
