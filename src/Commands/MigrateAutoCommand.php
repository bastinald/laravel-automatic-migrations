<?php

namespace Bastinald\LaravelAutomaticMigrations\Commands;

use Illuminate\Console\Command;
use Doctrine\DBAL\Schema\Comparator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class MigrateAutoCommand extends Command
{
    protected $signature = 'migrate:auto {--f|--fresh} {--s|--seed} {--force}';

    public function handle()
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->warn('Use the <info>--force</info> to migrate in production.');

            return;
        }

        $this->runTraditionalMigrations();
        $this->runAutomaticMigrations();
        $this->seed();

        $this->info('Automatic migration completed successfully.');
    }

    private function runTraditionalMigrations()
    {
        $command = 'migrate';

        if ($this->option('fresh')) {
            $command .= ':fresh';
        }

        if ($this->option('force')) {
            $command .= ' --force';
        }

        Artisan::call($command);
    }

    private function runAutomaticMigrations()
    {
        $path = is_dir(app_path('Models')) ? app_path('Models') : app_path();
        $namespace = app()->getNamespace();

        foreach ((new Finder)->in($path) as $model) {
            $model = $namespace . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($model->getRealPath(), realpath(app_path()) . DIRECTORY_SEPARATOR)
                );

            if (method_exists($model, 'migration')) {
                $this->migrate($model);
            }
        }
    }

    private function migrate($model)
    {
        $model = app($model);
        $modelTable = $model->getTable();
        $tempTable = 'table_' . $modelTable;

        Schema::dropIfExists($tempTable);
        Schema::create($tempTable, function (Blueprint $table) use ($model) {
            $model->migration($table);
        });

        if (Schema::hasTable($modelTable)) {
            $schemaManager = $model->getConnection()->getDoctrineSchemaManager();
            $modelTableDetails = $schemaManager->listTableDetails($modelTable);
            $tempTableDetails = $schemaManager->listTableDetails($tempTable);
            $tableDiff = (new Comparator)->diffTable($modelTableDetails, $tempTableDetails);

            if ($tableDiff) {
                $schemaManager->alterTable($tableDiff);

                $this->warn('Table updated: <info>' . $modelTable . '</info>');
            }

            Schema::drop($tempTable);
        } else {
            Schema::rename($tempTable, $modelTable);

            $this->warn('Table created: <info>' . $modelTable . '</info>');
        }
    }

    private function seed()
    {
        if ($this->option('seed')) {
            $command = 'db:seed';

            if ($this->option('force')) {
                $command .= ' --force';
            }

            Artisan::call($command, [], $this->getOutput());
        }
    }
}
