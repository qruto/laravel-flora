<p align="center">
    <img title="Laravel Initializer" height="100" src="docs/logo.png" />
</p>
<p align="center">A convenient way to <strong>initialize</strong> your project.</p>
<p align="center">
    <a href="https://packagist.org/packages/mad-web/laravel-initializer"><img src="https://img.shields.io/packagist/v/mad-web/laravel-initializer.svg" alt="Latest Stable Version"></a>
    <a href="https://travis-ci.org/mad-web/laravel-initializer"><img src="https://img.shields.io/travis/mad-web/laravel-initializer/master.svg" alt="Build Status"></a>
    <a href="https://styleci.io/repos/100302581"><img src="https://styleci.io/repos/100302581/shield?style=flat" alt="Code Style Status"></a>
    <a href="https://scrutinizer-ci.com/g/mad-web/laravel-initializer/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/mad-web/laravel-initializer.svg" alt="Code Coverage Status"></a>
    <a href="https://scrutinizer-ci.com/g/mad-web/laravel-initializer"><img src="https://img.shields.io/scrutinizer/g/mad-web/laravel-initializer.svg" alt="Quality Score"></a>
    <a href="https://packagist.org/packages/mad-web/laravel-initializer"><img src="https://img.shields.io/packagist/dt/mad-web/laravel-initializer.svg" alt="Quality Score"></a>
    <a href="LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg" alt="Software License"></a>
</p>

## Introduction

This package adds `app:install` and `app:update` artisan commands, which runs predefined actions depending on the current environment to initialize your application.
We all know that we have to document the installation process of the application in each project, and we also always write deploy scripts in Forge, Envoy.blade.php, ~~bash scripts~~ etc. With **Initializer** you have an ability to define these processes directly in application by simple commands chain.

## Installation

_*For Laravel <= 5.4*_ - Via Composer

``` bash
composer require mad-web/laravel-initializer:~0.1.0
```

add the service provider in `config/app.php` file:

```php
'providers' => [
    ...
    MadWeb\Initializer\InitializerServiceProvider::class,
];
```

_*For Laravel >= 5.5*_ - Via Composer

``` bash
composer require mad-web/laravel-initializer
```

Run `artisan make:initializers` command to create install and update config classes in `app` directory.

You can override config key which stores current environment value, just publish config file, and set `env_config_key` value.

```bash
php artisan vendor:publish --provider="MadWeb\Initializer\InitializerServiceProvider" --tag=config
```

_by default value is set to `app.env` for laravel, in most cases you don't need to override this value._

## Usage

Usage of `app:install` and `app:update` command are the same except that `app:install` uses `Install` class and `app:update` uses `Update` class.

Install class contents:

```php
namespace App;

use MadWeb\Initializer\Contracts\Runner;

class Install
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
use MadWeb\Initializer\Jobs\Supervisor\MakeQueueSupervisorConfig;
use MadWeb\Initializer\Jobs\Supervisor\MakeSocketSupervisorConfig;

class Install
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
            ->dispatch(new MakeQueueSupervisorConfig)
            ->dispatch(new MakeSocketSupervisorConfig)
            ->external('supervisorctl', 'reread')
            ->external('supervisorctl', 'update');
    }
}
```

Run it by passing "**root**" option:

```bash
artisan app:install --root
```

If you want to move config classes from the `app` directory to a different place, just rebind `project.installer` and `project.updater` keys in the `AppServiceProvider`.

```php
$this->app->bind('project.installer', \AnotherNameSpace\Install::class);
$this->app->bind('project.updater', \AnotherNameSpace\Update::class);
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

## Installation by one command

It would be nice to have ability to install an application by one command. We provide nice hack to implement this behavior.

Add `project-install` script into `scripts` section in `composer.json`.

```json
scripts": {
    ...
    "project-install": [
        "@composer install --no-scripts",
        "@php artisan app:install"
    ],
    ...
},
```

Then you can run just

```bash
composer project-install
```

to initialize your project.

If your application has commands that requires root privileges and you use Unix based system, add the following command into your runner chain.

```php
public function local(Runner $run)
{
    return $run
        ->artisan('key:generate')
        ->artisan('migrate')
        ->external('npm', 'install')
        ->external('npm', 'run', 'development')
        ->external('sudo', 'php', 'artisan', 'app:install', '--root');
}

public function localRoot(Runner $run)
{
    return $run
        ->dispatch(new MakeQueueSupervisorConfig)
        ->dispatch(new MakeSocketSupervisorConfig)
        ->external('supervisorctl', 'reread')
        ->external('supervisorctl', 'update');
}
```

## Upgrading

Please see [UPGRADING](UPGRADING.md) for details.

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

[link-author]: https://github.com/mad-web
[link-contributors]: ../../contributors
