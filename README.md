# Application installation command

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


This package adds `artisan app:install` command, which runs defined commands related to the current environment

## Installation

Via Composer

``` bash
$ composer require zfort/laravel-app-installer
```

Now add the service provider in config/app.php file:
```php
'providers' => [
    // ...
    ZFort\SocialAuth\InstallerServiceProvider::class,
];
```
Run `artisan make:installer` command to create installer config in `app` directory

## Usage

InstallerConfig contents:
```php
namespace App;

use ZFort\AppInstaller\Contracts\Runner;

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

You can add any another method which called the same as your environment name, for example `staging` and define different commands

If you need to run commands with root privileges separately you can define method with next convention
```php
namespace App;

use ZFort\AppInstaller\Contracts\Runner;

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
Run it by passing "root" option - `artisan app:insstall --root`

If you want to move config file from the `app` directory to a different place, just add a new binding in the `AppServiceProvider`
```php
$this->app->bind('project.installer', \AnotherNameSpace\InstallerConfig::class);
```

####List of commands available to run
```php
$run
    ->artisan('command', ['argument' => 'argument_value', '-param' => 'param_value', '--option' => 'option_value', ...]) // Artisan command
    ->external('command', 'argument', 'argument_value', '-param', 'param_value', '--option=option_value', ...) // Any external command
    ->callable('command', 'argument', ...) // Callable function (like for call_user_func)
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email developer@zfort.com instead of using the issue tracker.

## Credits

- [zfort](https://github.com/zfort)
- [All Contributors](../../contributors)

## About ZFort

ZFort Group is a full-scale IT outsourcing service provider that has delivered premium web development, consulting and B2B solutions since 2000.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/zfort/laravel-app-installer.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/zfort/laravel-app-installer/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/zfort/laravel-app-installer.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/zfort/laravel-app-installer.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/zfort/laravel-app-installer.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/zfort/laravel-app-installer
[link-travis]: https://travis-ci.org/zfort/laravel-app-installer
[link-scrutinizer]: https://scrutinizer-ci.com/g/zfort/laravel-app-installer/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/zfort/laravel-app-installer
[link-downloads]: https://packagist.org/packages/zfort/laravel-app-installer
[link-author]: https://github.com/zfort
[link-contributors]: ../../contributors