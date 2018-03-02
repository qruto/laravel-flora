# Application installation command

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-style]][link-style]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This package adds `artisan app:install` command, which runs defined commands related to the current environment.

## Installation

_*For Laravel <= 5.4*_ - Via Composer

``` bash
composer require mad-web/laravel-initializer:~0.1.0
```

_*For Laravel >= 5.5*_ - Via Composer

``` bash
composer require mad-web/laravel-initializer
```

_*For Laravel < 5.5*_ - add the service provider in `config/app.php` file:

```php
'providers' => [
    ...
    MadWeb\Initializer\InitializerServiceProvider::class,
];
```

Run `artisan make:installer` command to create installer config in `app` directory.

You can override config key which stores current environment value, just publish config file, and set `env_config_key` value.

```bash
php artisan vendor:publish --provider="MadWeb\Initializer\InitializerServiceProvider" --tag=config
```

_by default value is set to `app.env` for laravel, in most cases you don't need to override this value._

## Usage

InstallerConfig contents:

```php
namespace App;

use MadWeb\Initializer\Contracts\Runner;

class InstallerConfig
{
    public function production(Runner $run)
    {
        return $run
            ->artisan('key:generate')
            ->artisan('migrate')
            ->external('npm', 'install', '--production')
            ->external('npm', 'run', 'production')
            ->artisan('route:cache')
            ->artisan('config:cache')
            ->artisan('optimize')
            ->external('composer', 'dump-autoload', '--optimize');
    }

    public function local(Runner $run)
    {
        return $run
            ->artisan('key:generate')
            ->artisan('migrate')
            ->external('npm', 'install')
            ->external('npm', 'run', 'development');
    }
}
```

You can add any another method which should be called the same as your environment name, for example `staging`, and define different commands.

If you need to run commands with root privileges separately, you can define a method according to the following convention.

```php
namespace App;

use MadWeb\Initializer\Contracts\Runner;

class InstallerConfig
{
    public function local(Runner $run)
    {
        return $run
            ->artisan('key:generate')
            ->artisan('migrate')
            ->external('npm', 'install')
            ->external('npm', 'run', 'development');
    }

    public function localRoot(Runner $run)
    {
        return $run
            ->external('service', 'php-fpm', 'restart')
            ->external('supervisorctl', 'reread')
            ->external('supervisorctl', 'update');
    }
}
```

Run it by passing "**root**" option:

```bash
artisan app:install --root
```

If you want to move config file from the `app` directory to a different place, just rebind `project.installer` key in the `AppServiceProvider`.

```php
$this->app->bind('project.installer', \AnotherNameSpace\InstallerConfig::class);
```

### List of commands available to run

```php
$run
    ->artisan('command', ['argument' => 'argument_value', '-param' => 'param_value', '--option' => 'option_value', ...]) // Artisan command
    ->external('command', 'argument', '-param', 'param_value', '--option=option_value', ...) // Any external command by array
    ->external('command argument -param param_value --option=option_value') // Any external command by string
    ->callable('command', 'argument', ...) // Callable function (like for call_user_func)
    ->dispatch(new JobClass) // Dispatch job task
    ->dispatchNow(new JobClass) // Dispatch job task without queue
    ->publish(ServiceProvider::class) // Publish single service provider assets
    ->publish([
        ServiceProvider::class,
        AnotherServiceProvider::class,
    ]) // Publish multiple packages assets
    ->publish([ServiceProvider::class => 'tag']) // Publish package assets with tag
```

## Useful jobs

Laravel initializer provides some useful jobs to make initializing of your application much easier.

### Create cron task for scheduling tasks

To enable [Laravel Scheduling](https://laravel.com/docs/5.6/scheduling) add dispatch `MakeCronTask` job to runner chain to create cron task for your application.

```php
$run
    ...
    ->dispatch(new \MadWeb\Initializer\Jobs\MakeCronTask)
```

This job will add

```txt
* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```

to crontab list.

### Create laravel-echo-server.json config file

If you use [Laravel Echo Server](https://github.com/tlaverdure/laravel-echo-server) for broadcasting events in your application, add dispatch `MakeEchoServerConfig` job to runner chain to create configuration file for laravel-echo-server.

```php
$run
    ...
    ->dispatch(new \MadWeb\Initializer\Jobs\MakeEchoServerConfig);
```

It will create configuration file with default options of [laravel-echo-server](https://github.com/tlaverdure/laravel-echo-server) and prefilled values from your laravel application configuration.

You can override default value by passing array into the job constructor. It would be a good practice to create additional config value for **laravel-echo-server** in `broadcasting.php` config:

```php
/*
|--------------------------------------------------------------------------
| Laravel Echo server configurations
|--------------------------------------------------------------------------
|
| Here you may define all of laravel echo server options
|
*/
'server' => [
    'authEndpoint' => '/broadcasting/auth',
    'port' => env('SOCKET_PORT', '6001'),
    'sslCertPath' => env('SSL_CERT', ''),
    'sslKeyPath' => env('SSL_PATH', '')
],
```

And pass these values to `MakeEchoServerConfig` job constructor.

```php
$run
    ...
    ->dispatch(new \MadWeb\Initializer\Jobs\MakeEchoServerConfig(config('broadcasting.server')));
```

### Create supervisor config file for queues

This job creates supervisor config file for queue workers.
Just add dispatch `MakeQueueSupervisorConfig` job to runner chain.

```php
$run
    ...
    ->dispatch(new \MadWeb\Initializer\Jobs\Supervisor\MakeQueueSupervisorConfig);
```

This job creates configuration file with the command `php artisan queue:work --sleep=3 --tries=3` in `/etc/supervisor/conf.d/` folder by default, with a filename according to this convention `your-application-name-queue.conf`.

If you want to override default options just pass it into job constructor.
For example if you want to use [Laravel Horizon](https://laravel.com/docs/5.6/horizon) instead of default queue workers.

```php
$run
    ...
    ->dispatch(new \MadWeb\Initializer\Jobs\Supervisor\MakeQueueSupervisorConfig([
        'command' => 'php artisan horizon',
    ]));
```

### Create supervisor config file for laravel echo server

On the same way as `MakeQueueSupervisorConfig` this job creates supervisor config file for launching laravel echo server.
Just add dispatch `MakeSocketSupervisorConfig` job to runner chain. The difference from `MakeQueueSupervisorConfig` is the command `node ./node_modules/.bin/laravel-echo-server start` and the config filename is `your-application-name-socket.conf`.

Both config files save log files to `your-project-path/storage/logs`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email madweb.dev@gmail.com instead of using the issue tracker.

## Credits

- [Mad Web](https://github.com/mad-web)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/mad-web/laravel-initializer.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/mad-web/laravel-initializer/master.svg?style=flat-square
[ico-style]: https://styleci.io/repos/100302581/shield
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/mad-web/laravel-initializer.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/mad-web/laravel-initializer.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/mad-web/laravel-initializer.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/mad-web/laravel-initializer
[link-travis]: https://travis-ci.org/mad-web/laravel-initializer
[link-style]: https://styleci.io/repos/100302581
[link-scrutinizer]: https://scrutinizer-ci.com/g/mad-web/laravel-initializer/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/mad-web/laravel-initializer
[link-downloads]: https://packagist.org/packages/mad-web/laravel-initializer
[link-author]: https://github.com/mad-web
[link-contributors]: ../../contributors
