<p align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://github.com/qruto/laravel-formula/raw/HEAD/art/logo-dark.svg">
        <source media="(prefers-color-scheme: light)" srcset="https://github.com/qruto/laravel-formula/raw/HEAD/art/logo-light.svg">
        <img alt="Laravel Wave Logo" src="https://github.com/qruto/laravel-formula/raw/HEAD/art/logo-light.svg">
    </picture>
</p>
<p align="center">A convenient way to automate <strong>setup</strong> of your application.</p>
<p align="center">
    <a href="https://github.com/qruto/laravel-formula/actions/workflows/tests.yml"><img src="https://github.com/qruto/laravel-formula/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a>
    <a href="https://github.com/qruto/laravel-formula/actions/workflows/styles.yml"><img src="https://github.com/qruto/laravel-formula/actions/workflows/styles.yml/badge.svg" alt="Styles check"></a>
    <a href="https://github.com/qruto/laravel-formula/actions/workflows/types.yml"><img src="https://github.com/qruto/laravel-formula/actions/workflows/types.yml/badge.svg" alt="Types check"></a>
    <a href="https://github.com/qruto/laravel-formula/actions/workflows/refactor.yml"><img src="https://github.com/qruto/laravel-formula/actions/workflows/refactor.yml/badge.svg" alt="Refactor code"></a>
    <a href="https://packagist.org/packages/qruto/laravel-formula"><img src="https://img.shields.io/packagist/dt/qruto/laravel-formula" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/qruto/laravel-formula"><img src="https://img.shields.io/packagist/v/qruto/laravel-formula" alt="Latest Stable Version"></a>
</p>
<p align="center">
    <img width="600" alt="Laravel Formula Demo" src="https://github.com/qruto/laravel-formula/raw/HEAD/art/demo.png" />
</p>

## Introduction

Bring application to live by one command.
It will run chain of required commands to install or update application.

Replace ~~**installation**~~ section in readme file with:
```bash
php artisan install
```

Refresh application state.

- after `composer update`
- after `git pull/checkout/megre/...`
- in deploy script

Run:
```bash
php artisan update
```

it will take care of the rest of the work.

Add `update` command to your application `composer.json` script section:

```diff
"scripts": {
    "post-update-cmd": [
-        "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
+        "@php artisan update"
    ]
}
```

Define everything required to update application in one place.

**Laravel Formula** gives you the ability to declare these processes and run it by simple `app:install` and `app:update` artisan commands, which run predefined actions chain depending on the current environment.

> Put knowledge of the setup instructions at the application level

## Installation

Via Composer

``` bash
composer require qruto/laravel-formula
```

then publish formula classes:

```bash
php artisan vendor:publish --tag=formulas
```

It will create `Install` and `Update` classes in `app` directory
which contains `local` and `production` methods according to different environments.
This methods should return runner chain with specific actions to install or update processes.

You can override config key which stores current environment value, publish config file and set `env_config_key` value.

```bash
php artisan vendor:publish --provider="MadWeb\Formula\FormulaServiceProvider" --tag=config
```

## Usage

Usage of `app:install` and `app:update` command are the same except that `app:install` uses `Install` class and `app:update` uses `Update` class.

Install class contents:

```php
Runner::task('build', fn (Runner $run) =>
    $run->exec('npm run install')
        ->exec('npm run build')
);

Runner::task('cache', fn (Runner $run) =>
    $run->command('route:cache')
        ->command('config:cache')
        ->command('event:cache')
);
```

You can add any other method which should have the same name as your environment name, for example `staging`, and define different actions.

To see details of running actions use verbosity mode:

```bash
php artisan app:update -v
```

You can inject any service from [service container](https://laravel.com/docs/6.x/container) in constructor:

```php
class Update
{
    public function __construct(Filesystem $storage)
    {
        $this->storage = $storage;
    }
    // ...
}
```

### Runner API (available actions to run)

```php
$run
    ->command('command', ['argument' => 'argument_value', '-param' => 'param_value', '--option' => 'option_value', ...]) // Artisan command
    ->exec('command', 'argument', '-param', 'param_value', '--option=option_value', ...) // Any external command by arguments
    ->exec('command argument -param param_value --option=option_value') // Any external command by string
    ->call(function ($arg) {}, $arg) // Callable function (like for call_user_func)
    ->job(new JobClass) // Dispatch job task
    ->jobNow(new JobClass) // Dispatch job task without queue
    ->publish(ServiceProvider::class) // Publish single service provider assets
    ->publish([
        ServiceProvider::class,
        AnotherServiceProvider::class,
    ]) // Publish multiple packages assets
    ->publish([ServiceProvider::class => 'public']) // Publish package assets with tag
    ->publish([ServiceProvider::class => ['public', 'assets']]) // Publish package assets with multiple tags
    ->publishForce(ServiceProvider::class) // Force publish, works in any variations
    ->publishTag('public') // Publish specific tag
    ->publishTag(['public', 'assets']) // Publish multiple tags
    ->publishTagForce('public') // Force publish tags
```

### Laravel Nova

If you use [Laravel Nova](https://nova.laravel.com), don't forget to publish **Nova** assets on each update:

```php
// Update class
$run
    ...
    ->artisan('nova:publish')
    // or
    ->publishTag('nova-assets')
```

## Useful jobs

Laravel formula provides some useful jobs to make setup of your application much easier.

### Create cron task for scheduling tasks

To enable [Laravel Scheduling](https://laravel.com/docs/6.x/scheduling) add dispatch `MakeCronTask` job to runner chain to create cron task for your application.

```php
$run
    ...
    ->dispatch(new \MadWeb\Formula\Jobs\MakeCronTask)
```

This job will add

```txt
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

to crontab list.

## Installation by one command

For running `php artisan app:install` command, you should install composer dependencies at first.
It would be nice to have the ability to install an application by one command. We provide nice hack to implement this behavior.

Add `app-install` script into `scripts` section in `composer.json`.

```json
"scripts": {
    "app-install": [
        "@composer install",
        "@php artisan app:install"
    ],
}
```

Then you can run just

```bash
composer app-install
```

to setup your application.

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

If you discover any security related issues, please email madweb.dev@gmail.com instead of using the issue tracker.

## Credits

Thanks [Nuno Maduro](https://github.com/nunomaduro) for [laravel-console-task](https://github.com/nunomaduro/laravel-console-task) package which gives pretty tasks outputs

- [Qruto](https://github.com/qruto)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/qruto
[link-contributors]: ../../contributors
