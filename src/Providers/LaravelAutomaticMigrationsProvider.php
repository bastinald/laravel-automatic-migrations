<?php

namespace Bastinald\LaravelAutomaticMigrations\Providers;

use Bastinald\LaravelAutomaticMigrations\Commands\MakeFactoryCommand;
use Bastinald\LaravelAutomaticMigrations\Commands\MigrateAutoCommand;
use Bastinald\LaravelAutomaticMigrations\Commands\MakeModelCommand;
use Illuminate\Support\ServiceProvider;

class LaravelAutomaticMigrationsProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFactoryCommand::class,
                MigrateAutoCommand::class,
                MakeModelCommand::class,
            ]);
        }
    }
}
