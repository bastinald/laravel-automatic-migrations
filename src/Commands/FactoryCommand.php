<?php

namespace Bastinald\Migrations\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class FactoryCommand extends FactoryMakeCommand
{
    protected $name = 'make:afactory';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/factory.stub';
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the factory already exists'],
        ];
    }
}
