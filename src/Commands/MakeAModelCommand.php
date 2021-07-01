<?php

namespace Bastinald\LaravelAutomaticMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Livewire\Commands\ComponentParser;

class MakeAModelCommand extends Command
{
    protected $signature = 'make:amodel {class} {--f|--factory} {--force}';
    private $modelParser;

    public function handle()
    {
        $this->modelParser = new ComponentParser(
            is_dir(app_path('Models')) ? 'App\\Models' : 'App',
            config('livewire.view_path'),
            $this->argument('class')
        );

        if (file_exists($this->modelParser->classPath()) && !$this->option('force')) {
            $this->warn('Model exists: <info>' . $this->modelParser->relativeClassPath() . '</info>');
            $this->warn('Use the <info>--force</info> to overwrite it.');

            return;
        }

        if ($this->modelParser->className() == 'User') {
            $this->deleteUserMigration();
        }

        $this->makeStub();

        if ($this->option('factory')) {
            $this->makeFactory();
        }
    }

    private function deleteUserMigration()
    {
        $userMigrationName = 'database/migrations/2014_10_12_000000_create_users_table.php';
        $userMigrationFile = base_path($userMigrationName);

        if (file_exists($userMigrationFile)) {
            unlink($userMigrationFile);

            $this->warn('Migration deleted: <info>' . $userMigrationName . '</info>');
        }
    }

    private function makeStub()
    {
        $replaces = [
            'DummyModelClass' => $this->modelParser->className(),
            'DummyModelNamespace' => $this->modelParser->classNamespace(),
        ];
        $stub = $this->modelParser->className() == 'User' ? 'UserModel.php' : 'Model.php';

        $contents = str_replace(
            array_keys($replaces),
            $replaces,
            file_get_contents(config('laravel-automatic-migrations.stub_path') . '/' . $stub)
        );

        file_put_contents($this->modelParser->classPath(), $contents);

        $this->warn('Model made: <info>' . $this->modelParser->relativeClassPath() . '</info>');
    }

    private function makeFactory()
    {
        Artisan::call('make:afactory', [
            'class' => $this->modelParser->className() . 'Factory',
            '--force' => $this->option('force'),
        ], $this->getOutput());
    }
}
