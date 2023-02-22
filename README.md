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
It will run chain of required commands to install or update the application.

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

Define everything required to update application in one place.

**Laravel Formula** gives you the ability to declare these processes and run it by simple `app:install` and `app:update` artisan commands, which run predefined actions chain depending on the current environment.

> 🧠🚀 Put knowledge of the setup instructions at the application level

## Installation

Via Composer

``` bash
composer require qruto/laravel-formula
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

ℹ️ Instruction depends on current application environment. It will run chain of predefined actions suitable for most cases.

To customize Formula instructions for each environment, you need to publish config files.

```bash
php artisan vendor:publish --tag=formulas
```

Open `routes/build.php` file.

```php
use Qruto\Formula\Run;

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

There you can find instructions for `local` and `production` environments.
Feel free to change it any way you need or add your specific environment like `staging`.

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

See detailed output in verbosity mode:

```bash
php artisan app:update -v
```

### Custom Scripts

Define custom script calling `Run::newScript` in service provider's `boot` method:

```php
Run::newScript('some', fn (Run $run) => $run
    ->exec('some command')
    ->exec('another command')
);
```

### Runner API (available actions to run)

```php
$run
    ->command('command', ['argument' => 'argument_value', '-param' => 'param_value', '--option' => 'option_value', ...]) // Artisan command
    ->exec('command', 'argument', '-param', 'param_value', '--option=option_value', ...) // Any external command by arguments
    ->exec('command argument -param param_value --option=option_value') // Any external command by string
    ->call(function ($arg) {}, $arg) // Callable function (like for call_user_func)
    ->job(new JobClass) // Dispatch job task
    ->script('build') // Run custom script
    ->notification('Some message') // Send notification to Slack
```

### Package Actions

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

Add `update` command to your application `composer.json` script section:

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

If you discover any security related issues, please email madweb.dev@gmail.com instead of using the issue tracker.

## Credits

Thanks [Nuno Maduro](https://github.com/nunomaduro) for [laravel-console-task](https://github.com/nunomaduro/laravel-console-task) package which gives pretty tasks outputs

- [Qruto](https://github.com/qruto)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/qruto
[link-contributors]: ../../contributors
