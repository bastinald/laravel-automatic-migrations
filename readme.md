# Laravel Automatic Migrations

Instead of having to create and manage migration files, this package allows you to specify your migrations inside your model classes via a `migration` method. When you run the `migrate:auto` command, it uses Doctrine to compare your model `migration` methods to the existing schema, and applies the changes automatically.

This package works fine alongside traditional Laravel migration files, for the cases where you still need migrations that are not coupled to a model. When you run the `migrate:auto` command, it will run your traditional migrations first, and the automatic migrations afterwards.

## Documentation

- [Installation](#installation)
- [Usage](#usage)
- [Commands](#commands)
    - [Making Models](#making-models)
    - [Running Migrations](#running-migrations)
- [Migration Order](#migration-order)
- [Publishing Stubs](#publishing-stubs)

## Installation

Require the package via composer:

```console
composer require bastinald/laravel-automatic-migrations
```

## Usage

Declare a `migration` method in your models:

 ```php
namespace App\Models;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class MyModel extends Model
{
    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('name');
        $table->timestamp('created_at')->nullable();
        $table->timestamp('updated_at')->nullable();
    }
}
 ```

Run the `migrate:auto` command:

```console
php artisan migrate:auto
```

## Commands

### Making Models

Make a model with a `migration` method included:

```console
php artisan make:amodel {class} {--force}
```

This command will also make a factory whose `definition` points to the model method. Use `--force` to overwrite an existing model.

### Running Migrations

Run automatic migrations:

```console
php artisan migrate:auto {--f|--fresh} {--s|--seed} {--force}
```

Use `-f` to wipe the database, `-s` to seed after migration, and `--force` to run migrations in production.

## Migration Order

You can specify the order to run your model migrations by adding a public `migrationOrder` property to your models. This is useful for pivot tables or situations where you must create a certain table before another.

```php
class MyModel extends Model
{
    public $migrationOrder = 1;

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('name');
        $table->timestamp('created_at')->nullable();
        $table->timestamp('updated_at')->nullable();
    }
}
```

The `migrate:auto` command will run the automatic migrations in the order specified. If no order is declared for a model, it will default to `0`. Thanks to [@vincentkedison](https://github.com/vincentkedison) for this idea.

## Publishing Stubs

Use your own model and factory stubs by publishing package files:

```console
php artisan vendor:publish --tag=laravel-automatic-migrations
```

Update the `stub_path` in `config/laravel-automatic-migrations.php`:

```php
'stub_path' => resource_path('stubs/vendor/laravel-automatic-migrations'),
```

Now edit the stub files inside `resources/stubs/vendor/laravel-automatic-migrations`. Commands will now use these stub files to make models and factories.
