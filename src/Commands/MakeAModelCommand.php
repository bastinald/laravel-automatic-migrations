<?php

namespace Bastinald\LaravelAutomaticMigrations\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class MakeAModelCommand extends ModelMakeCommand
{
    protected $name = 'make:amodel';
    protected $studlyName;

    public function handle()
    {
        $this->studlyName = Str::studly($this->argument('name'));

        $this->renameUserMigration();
        $this->createFactory();

        return parent::handle();
    }

    protected function renameUserMigration()
    {
        if ($this->studlyName == 'User') {
            $migration = database_path('migrations/2014_10_12_000000_create_users_table.php');

            if (file_exists($migration)) {
                rename($migration, $migration . '.bak');
            }
        }
    }

    protected function createFactory()
    {
        $this->call('make:afactory', [
            'name' => $this->studlyName,
            '--force' => $this->option('force'),
        ]);
    }

    protected function getStub()
    {
        $stub = $this->studlyName == 'User' ? 'user-model' : 'model';

        return rtrim(config('laravel-automatic-migrations.stub_path'), '/') . '/' . $stub . '.stub';
    }
}
