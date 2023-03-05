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

- `MakeCronJob` removed. During installation process in `production` environment, you will be asked to create a cron job for running task scheduler.
