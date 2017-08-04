# Very short description of the package

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

**Note:** Replace ```zfort``` ```zfort``` ```developers@zfort.com``` ```laravel-app-installer``` ```Package provider ability to setup pre/post install/update actions``` with their correct values in [README.md](README.md), [CHANGELOG.md](CHANGELOG.md), [CONTRIBUTING.md](CONTRIBUTING.md), [LICENSE.md](LICENSE.md) and [composer.json](composer.json) files, then delete this line.

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Buchmy St, 1-B, 3rd floor, Kharkiv, Ukraine.

We publish all received postcards [on our company website](https://zfort.com/en/opensource/postcards).

## Structure

If any of the following are applicable to your project, then the directory structure should follow industry best practises by being named the following.

```
bin/        
config/
src/
tests/
vendor/
```

## Installation

**Note:** Remove this paragraph if you are building a public package  
This package is custom built for [ZFort](https://zfort.com) projects and is therefore not registered on packagist. In order to install it via composer you must specify this extra repository in `composer.json`:

```json
"repositories": [ { "type": "composer", "url": "https://satis.zfort.com/" } ]
```

You can install the package via composer:

```bash
composer require zfort/laravel-app-installer
```

## Usage

``` php
$skeleton = new ZFort\Skeleton();
echo $skeleton->echoPhrase('Hello, ZFort!');
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