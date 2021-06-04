<?php

namespace Bastinald\LaravelAutomaticMigrations\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class MakeAModelCommand extends ModelMakeCommand
{
    protected $name = 'make:amodel';

    public function handle()
    {
        $this->call('make:afactory', [
            'name' => Str::studly($this->argument('name')),
            '--force' => $this->option('force'),
        ]);

        return parent::handle();
    }

    protected function getStub()
    {
        $stub = Str::studly($this->argument('name')) == 'User' ? 'user-model' : 'model';

        return rtrim(config('laravel-automatic-migrations.stub_path'), '/') . '/' . $stub . '.stub';
    }
}
