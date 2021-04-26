<?php

namespace Bastinald\Migrations\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class ModelCommand extends ModelMakeCommand
{
    protected $name = 'make:amodel';
    private $studlyName;

    public function handle()
    {
        $this->studlyName = Str::studly($this->argument('name'));

        if ($this->studlyName == 'User') {
            $migration = database_path('migrations/2014_10_12_000000_create_users_table.php');

            if (file_exists($migration)) {
                rename($migration, $migration . '.bak');
            }
        }

        $this->createFactory();

        return parent::handle();
    }

    protected function getStub()
    {
        if ($this->studlyName == 'User') {
            $stub = 'user.stub';
        } else {
            $stub = 'model.stub';
        }

        return __DIR__ . '/../../stubs/' . $stub;
    }

    protected function createFactory()
    {
        $this->call('make:afactory', array_merge([
            'name' => "{$this->studlyName}Factory",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ], $this->option('force') ? [
            '--force' => true,
        ] : []));
    }
}
