# Laravel Automatic Migrations

Automatic Laravel model migrations. Instead of having to create and manage migration files, this package allows you to specify your migrations inside your model classes via a `migration` method. When you run the `migrate:auto` command, it uses Doctrine to compare your model `migration` methods to the existing schema, and applies the changes automatically.

This package works perfectly fine alongside traditional Laravel migration files, for the edge cases where you still need migrations that are not coupled to a model. When you run the `migrate:auto` command, it will run your traditional migrations first, and the automatic migrations afterwards.

## Installation

Require the package via composer:

```console
composer require bastinald/laravel-automatic-migrations
```

## Usage

Declare a `migration` method in your models:

 ```php
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

#### Making Models

Make a model with a `migration` method included:

```console
php artisan make:amodel {name}
```

#### Making Factories

Make a factory whose `definition` points to a model:

```console
php artisan make:afactory {name}
```

#### Running Automatic Migrations

Run automatic migrations:

```console
php artisan migrate:auto {--f|--fresh} {--s|--seed} {--force}
```

## Custom Stubs

Use your own model and factory stubs by publishing package files:

```console
php artisan vendor:publish --tag=laravel-automatic-migrations
```

Update the `stub_path` in `config/laravel-automatic-migrations.php`:

```php
'stub_path' => base_path('resources/stubs/vendor/laravel-automatic-migrations'),
```

Now just edit the stub files inside `resources/stubs/vendor/laravel-automatic-migrations` to your needs. The commands will now use these stub files to make models and factories.

## Traits

#### HasHashes

This trait will automatically hash attributes specified via a `$hashes` property in your model. It will only do so if the values are not already hashed, so it does not slow down seeders.

```php
use Bastinald\LaravelAutomaticMigrations\Traits\HasHashes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasHashes;

    protected $hashes = ['password'];
}
```
