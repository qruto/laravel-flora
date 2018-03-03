# Upgrading

Some versions has breaking changes. if you want to upgrade **laravel-initializer** version to latest and don't break a current behavior, you need to use this upgrade guide.

## From v0.* to v1.0

- the name of installation class has changed from `InstallerConfig` to `Install`.
  If you use default class name and location please change it to `Install`.
- constructor parameter signature for `Executor` contract changed from `$installCommand` to `$artisanCommand`. If you implement it with custom class, change constructor parameter signature.