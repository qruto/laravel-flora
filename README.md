<p align="center">
    <picture>
        <source
            width="350"
            media="(prefers-color-scheme: dark)"
            srcset="https://github.com/qruto/laravel-flora/raw/HEAD/art/logo-dark.png"
        >
        <source
            width="350"
            media="(prefers-color-scheme: light)"
            srcset="https://github.com/qruto/laravel-flora/raw/HEAD/art/logo-light.png"
        >
        <img
            width="350"
            alt="Laravel Flora Logo"
            src="https://github.com/qruto/laravel-flora/raw/HEAD/art/logo-light.png"
        >
    </picture>
</p>

<p align="center">
    <picture>
        <source
            width="650"
            media="(prefers-color-scheme: dark)"
            srcset="https://github.com/qruto/laravel-flora/raw/HEAD/art/code-example-dark.svg"
        >
        <source
            width="650"
            media="(prefers-color-scheme: light)"
            srcset="https://github.com/qruto/laravel-flora/raw/HEAD/art/code-example-light.svg"
        >
        <img
            width="650"
            alt="Laravel Flora Code Example"
            src="https://github.com/qruto/laravel-flora/raw/HEAD/art/code-example-light.svg"
        >
    </picture>
</p>

<p align="center">A convenient way to automate <strong>setup</strong> of your application.</p>
<p align="center">
    <a href="https://github.com/qruto/laravel-flora/actions/workflows/tests.yml"><img src="https://github.com/qruto/laravel-flora/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a>
    <a href="https://github.com/qruto/laravel-flora/actions/workflows/styles.yml"><img src="https://github.com/qruto/laravel-flora/actions/workflows/styles.yml/badge.svg" alt="Styles check"></a>
    <a href="https://github.com/qruto/laravel-flora/actions/workflows/types.yml"><img src="https://github.com/qruto/laravel-flora/actions/workflows/types.yml/badge.svg" alt="Types check"></a>
    <a href="https://github.com/qruto/laravel-flora/actions/workflows/refactor.yml"><img src="https://github.com/qruto/laravel-flora/actions/workflows/refactor.yml/badge.svg" alt="Refactor code"></a>
    <a href="https://packagist.org/packages/qruto/laravel-flora"><img src="https://img.shields.io/packagist/dt/qruto/laravel-flora" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/qruto/laravel-flora"><img src="https://img.shields.io/packagist/v/qruto/laravel-flora" alt="Latest Stable Version"></a>
</p>
<p align="center">
    <img width="650" alt="Laravel Flora Demo" src="/art/demo.png" />
</p>

## Goal

The main goal of _Flora_ is define and automate the setup process of Laravel application.
All necessary actions to make the application ready to work in one place.

Packages discovering, assets building and publishing, running database migrations, caching etc...

> 🧠🚀 Put the knowledge of setup instructions at the application level.

## Introduction

Revival of [Laravel Initializer](https://laravel-news.com/automate-app-setup-with-laravel-initializer). Rethinked, improved, prettified, renamed.

_Flora_ allows you to bring Laravel application to live by one command.
Use default or define custom chain of actions required to **install** or **update** an application.

Run `install` when you fetch a fresh application to prepare it to launch on new environment.

- after `git clone`

Run `update` on every dependency or source code change.

- after `composer install|update`
- after `git pull|checkout|megre|...`
- in deploy script
- in CI/CD pipeline

it will take care of the rest of the work.

## Support

Since of February 24, unfortunately I haven't any commercial work, permanent living place or the ability to plan anything for the long term. However, I have a greater desire to continue creating useful solutions for people around the world. It makes me feel better these days.

[![support me](https://raw.githubusercontent.com/slavarazum/slavarazum/main/support-banner.png)](https://github.com/sponsors/qruto)

[GitHub Sponsorships profile](https://github.com/sponsors/qruto) is ready! There you can find current work, future plans, goals and dreams...
Your stars make me happier each day ❤️ ⭐ Sponsorship will enable us to live more peacefully and continue to work on useful solutions for you.

I would be very grateful for mentions or just a sincere "thank you".

💳 [Sponsoring directly to savings jar](https://send.monobank.ua/jar/3eG4Vafvzq) with card or Apple Pay/Google Pay.

## Installation

Via Composer:

``` bash
composer require qruto/laravel-flora
```

## Usage

Replace ~~**installation**~~ section in readme file with:

```bash
php artisan install
```

Refresh application state by:

```bash
php artisan update
```

> ℹ️ Instruction depends on current **environment**. Package has predefined actions suitable for most cases.

See detailed output in verbosity mode:

```bash
php artisan app:update -v
```

You can automate the update process by adding `@php artisan update` command to your application
`composer.json` script `post-autoload-dump` section and remove
default `vendor:publish` command from `post-update-cmd` section.
`update` command will take care of assets publishing for you.

Setup it with:

```bash
php artisan flora:setup --script
```

`composer.json` changes:

```diff
"post-autoload-dump": [
    "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump", 
-     "@php artisan package:discover --ansi"
+     "@php artisan update"
],
- "post-update-cmd": [
-     "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
- ],
```

### Register Task Scheduler

Conditions:
- any scheduler task registered
- installation process performed
- application in production environment

then you will be prompted for the addition of a cron entry to [run the task scheduler every minute](https://laravel.com/docs/master/scheduling#running-the-scheduler).

![Task Scheduler](https://github.com/qruto/laravel-flora/raw/HEAD/art/task-scheduling.png)

## Configuration

To customize instructions for each environment, you need to publish setup files.

```bash
php artisan flora:setup
```

This command will create `routes/setup.php` file with predefined instructions for `local` and `production` environments.

```php
use Qruto\Flora\Run;

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

In addition, it will create `config/flora.php` for configuration assets publishing.

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
php artisan vendor:publish --tag=flora-config
```

### Side Packages Support

_Flora_ automatically detects several packages for performing necessary actions on install or update.
For example: publish Vapor UI assets, generate IDE helper files, terminate Horizon workers etc.

Supported:

- [Laravel Vapor Ui](https://github.com/laravel/vapor-ui)
- [Laravel Horizon](https://github.com/laravel/horizon)
- [IDE Helper for Laravel](https://github.com/barryvdh/laravel-ide-helper)

Soon:

- [ ] [Laravel Octane](https://laravel.com/docs/10.x/octane#reloading-the-workers)
- [ ] [Laravel Nova](https://nova.laravel.com/docs/4.0/installation.html#updating-nova-s-assets)
- [ ] [Laravel Passport](https://laravel.com/docs/10.x/passport#deploying-passport)

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
