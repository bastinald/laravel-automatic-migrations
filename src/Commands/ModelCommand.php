<?php

namespace Bastinald\Migrations\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class ModelCommand extends ModelMakeCommand
{
    protected $name = 'make:amodel';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/model.stub';
    }

    protected function createFactory()
    {
        $factory = Str::studly($this->argument('name'));

        $this->call('make:afactory', [
            'name' => "{$factory}Factory",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }
}
