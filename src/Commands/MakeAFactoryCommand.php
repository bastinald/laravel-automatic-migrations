<?php

namespace Bastinald\LaravelAutomaticMigrations\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeAFactoryCommand extends FactoryMakeCommand
{
    protected $name = 'make:afactory';

    protected function getStub()
    {
        $studlyName = Str::studly($this->argument('name'));
        $stub = in_array($studlyName, ['User', 'UserFactory']) ? 'user-factory' : 'factory';

        return rtrim(config('laravel-automatic-migrations.stub_path'), '/') . '/' . $stub . '.stub';
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the factory already exists'],
        ];
    }
}
