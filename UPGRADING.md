# Upgrading

Some versions has breaking changes. if you want to upgrade **laravel-flora** version to latest and don't break a current behavior, you need to use this upgrade guide.

## From `laravel-initializer` to `laravel-flora`

Supports PHP **^8.1** and Laravel **^10.0**

- The `install` and `update` commands now have no `app` prefix. Check out [usage](https://github.com/qruto/laravel-flora/README.md#usage) section.
- The `Install` and `Update` classes also removed. All instructions now are in `routes/setup.php` file. 
Look at current [configuration](https://github.com/qruto/laravel-flora/README.md#configuration) api.

`Run` object methods renamed to more familiar from [Scheduler](https://laravel.com/docs/master/scheduling):

- `artisan` to `command`
- `callable` to `call`
- `external` to `exec`
- `dispatch` to `job`

Assets list for publishing moved to config file.

## From v0.* to v1.0

- the name of installation class has changed from `InstallerConfig` to `Install`.
  If you use default class name and location please change it to `Install`.
- constructor parameter signature for `Executor` contract changed from `$installCommand` to `$artisanCommand`. If you implement it with custom class, change constructor parameter signature.

## From v1.* to v2.*

This update doesn't affect most users if you haven't override flora classes. If you have, checkout next breaking changes:

- `Executor` class and contract removed, only `Runner` contract and class left
- `Runner` contract changed. If you are implementing this interface manually, you should adopt your implementation.
- Renamed `project.installer` and `project.updater` service container abstractions to `app.installer` and `app.updater`

Also default flora stubs updated, you may copy new content.
