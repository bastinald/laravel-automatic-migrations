<?php

namespace Bastinald\LaravelAutomaticMigrations\Providers;

use Bastinald\LaravelAutomaticMigrations\Commands\MakeAFactoryCommand;
use Bastinald\LaravelAutomaticMigrations\Commands\MakeAModelCommand;
use Bastinald\LaravelAutomaticMigrations\Commands\MigrateAutoCommand;
use Illuminate\Support\ServiceProvider;

class LaravelAutomaticMigrationsProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAFactoryCommand::class,
                MigrateAutoCommand::class,
                MakeAModelCommand::class,
            ]);
        }

        $this->publishes(
            [__DIR__ . '/../../config/laravel-automatic-migrations.php' => config_path('laravel-automatic-migrations.php')],
            ['laravel-automatic-migrations', 'laravel-automatic-migrations:config']
        );

        $this->publishes(
            [__DIR__ . '/../../resources/stubs' => resource_path('stubs/vendor/laravel-automatic-migrations')],
            ['laravel-automatic-migrations', 'laravel-automatic-migrations:stubs']
        );
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/laravel-automatic-migrations.php', 'laravel-automatic-migrations');
    }
}
