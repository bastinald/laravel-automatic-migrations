<?php

namespace Bastinald\LaravelAutomaticMigrations\Commands;

use Illuminate\Console\Command;
use Doctrine\DBAL\Schema\Comparator;
use Illuminate\Support\Arr;
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

        Artisan::call($command, [], $this->getOutput());
    }

    private function runAutomaticMigrations()
    {
        $paths = Arr::wrap(config('laravel-automatic-migrations.model_paths'));
        $finder = new Finder;

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            foreach ($finder->in($path) as $file) {
                preg_match('/namespace (.*);/', $file->getContents(), $matches);

                $class = str_replace('.php', '', $matches[1] . '\\' . $file->getFilename());

                if (method_exists($class, 'migration')) {
                    $this->migrate($class);
                }
            }
        }
    }

    private function migrate($class)
    {
        $model = app($class);
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

                $this->line('<info>Table updated:</info> ' . $modelTable);
            }

            Schema::drop($tempTable);
        } else {
            Schema::rename($tempTable, $modelTable);

            $this->line('<info>Table created:</info> ' . $modelTable);
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
