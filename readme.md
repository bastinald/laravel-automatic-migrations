# Laravel Automatic Migrations

Automatic migrations for your Laravel models.

## Installation

```console
composer require bastinald/laravel-automatic-migrations
```

## Usage

Declare a `migration` method in your Laravel models:

```php
public function migration(Blueprint $table)
{
    $table->id();
    $table->string('name');
    $table->timestamp('created_at')->nullable();
    $table->timestamp('updated_at')->nullable();
}
```

Run the `migrate:auto` command:

```console
php artisan migrate:auto
```

## Commands

Make a model with a `migration` method included:

```console
php artisan make:amodel {name}
```

Make a factory whose `definition` points to a model:

```console
php artisan make:afactory {name}
```

Run automatic migrations:

```console
php artisan migrate:auto {--fresh} {--seed} {--force}
```
