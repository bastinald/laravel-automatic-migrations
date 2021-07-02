<?php

namespace Bastinald\LaravelAutomaticMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Livewire\Commands\ComponentParser;

class MakeAModelCommand extends Command
{
    protected $signature = 'make:amodel {class} {--force}';
    private $filesystem;
    private $modelParser;
    private $factoryParser;
    private $replaces;

    public function handle()
    {
        $this->filesystem = new Filesystem;

        $this->modelParser = new ComponentParser(
            is_dir(app_path('Models')) ? 'App\\Models' : 'App',
            config('livewire.view_path'),
            $this->argument('class')
        );

        $this->factoryParser = new ComponentParser(
            'Database\\Factories',
            config('livewire.view_path'),
            $this->modelParser->className() . 'Factory'
        );

        if ($this->filesystem->exists($this->modelParser->classPath()) && !$this->option('force')) {
            $this->warn('Model exists: <info>' . $this->modelParser->className() . '</info>');
            $this->warn('Use the <info>--force</info> to overwrite it.');

            return;
        }

        $this->deleteUserMigrations();
        $this->setReplaces();
        $this->makeStubs();

        $this->warn('Model made: <info>' . $this->modelParser->className() . '</info>');
        $this->warn('Factory made: <info>' . $this->factoryParser->className() . '</info>');
    }

    private function deleteUserMigrations()
    {
        if ($this->modelParser->className() == 'User') {
            $names = ['create_users_table', 'add_timezone_column_to_users_table'];

            foreach ($this->filesystem->allFiles(database_path('migrations')) as $file) {
                if (Str::contains($file, $names)) {
                    $this->filesystem->delete($file);

                    $this->warn('Migration deleted: <info>' . $file->getRelativePathname() . '</info>');
                }
            }
        }
    }

    private function setReplaces()
    {
        $this->replaces = [
            'DummyFactoryClass' => $this->factoryParser->className(),
            'DummyFactoryNamespace' => $this->factoryParser->classNamespace(),
            'DummyModelClass' => $this->modelParser->className(),
            'DummyModelNamespace' => $this->modelParser->classNamespace(),
        ];
    }

    private function makeStubs()
    {
        $this->filesystem->put(
            Str::replace('//', '/', $this->modelParser->classPath()),
            $this->replaceStub($this->modelParser->className() == 'User' ? 'UserModel.php' : 'Model.php')
        );

        $this->filesystem->put(
            Str::replaceFirst('app/', '', Str::replace('//', '/', $this->factoryParser->classPath())),
            $this->replaceStub($this->modelParser->className() == 'User' ? 'UserFactory.php' : 'Factory.php')
        );
    }

    private function replaceStub($stub)
    {
        return Str::replace(
            array_keys($this->replaces),
            $this->replaces,
            $this->filesystem->get(config('laravel-automatic-migrations.stub_path') . '/' . $stub)
        );
    }
}
