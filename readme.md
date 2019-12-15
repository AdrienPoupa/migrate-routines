## Migrate Routines for Laravel

[![Latest Stable Version](https://poser.pugx.org/adrienpoupa/migrate-routines/version.png)](https://packagist.org/packages/adrienpoupa/migrate-routines)
[![Total Downloads](https://poser.pugx.org/adrienpoupa/migrate-routines/d/total.png)](https://packagist.org/packages/adrienpoupa/migrate-routines)

Generate Laravel Migrations from existing MySQL routines: views, procedures, functions and triggers

### Installation


Require this package with composer. It is recommended to only require the package for development.

```shell
composer require adrienpoupa/migrate-routines --dev
```

### Usage

Convert the existing views into migrations

```shell
php artisan migrate:views
```

Convert the existing procedures into migrations

```shell
php artisan migrate:procedures
```

Convert the existing functions into migrations

```shell
php artisan migrate:functions
```

Convert the existing triggers into migrations

```shell
php artisan migrate:triggers
```

For all the commands, is possible to specify the database from which to retrieve the routines with the `--database` option, like this:

```shell
php artisan migrate:views --database=database_name
```

For this package to work, your database connection should be done with a user privileged enough to run elevated queries
from the information_schema and the mysql.proc tables.
