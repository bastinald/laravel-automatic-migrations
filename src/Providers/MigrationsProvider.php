<?php

namespace Bastinald\Migrations\Providers;

use Bastinald\Migrations\Commands\FactoryCommand;
use Bastinald\Migrations\Commands\MigrateCommand;
use Bastinald\Migrations\Commands\ModelCommand;
use Illuminate\Support\ServiceProvider;

class MigrationsProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FactoryCommand::class,
                MigrateCommand::class,
                ModelCommand::class,
            ]);
        }
    }
}
