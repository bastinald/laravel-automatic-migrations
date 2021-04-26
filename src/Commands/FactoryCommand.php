<?php

namespace Bastinald\Migrations\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;

class FactoryCommand extends FactoryMakeCommand
{
    protected $name = 'make:afactory';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/factory.stub';
    }
}
