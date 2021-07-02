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
            $this->argument('class') . 'Factory'
        );

        if ($this->filesystem->exists($this->modelParser->classPath()) && !$this->option('force')) {
            $this->warn('Model exists: <info>' . $this->modelParser->relativeClassPath() . '</info>');
            $this->warn('Use the <info>--force</info> to overwrite it.');

            return;
        }

        $this->deleteUserMigrations();
        $this->makeStubs();

        $this->warn('Model made: <info>' . $this->modelParser->relativeClassPath() . '</info>');
        $this->warn('Factory made: <info>' . $this->replaceFactoryPath('relativeClassPath') . '</info>');
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

    private function makeStubs()
    {
        $replaces = [
            'DummyFactoryClass' => $this->factoryParser->className(),
            'DummyFactoryNamespace' => $this->factoryParser->classNamespace(),
            'DummyModelClass' => $this->modelParser->className(),
            'DummyModelNamespace' => $this->modelParser->classNamespace(),
        ];

        $stubPath = config('laravel-automatic-migrations.stub_path');
        $modelStub = $this->modelParser->className() == 'User' ? 'UserModel.php' : 'Model.php';
        $factoryStub = $this->modelParser->className() == 'User' ? 'UserFactory.php' : 'Factory.php';

        $modelContents = str_replace(
            array_keys($replaces),
            $replaces,
            $this->filesystem->get($stubPath . DIRECTORY_SEPARATOR . $modelStub)
        );

        $factoryContents = str_replace(
            array_keys($replaces),
            $replaces,
            $this->filesystem->get($stubPath . DIRECTORY_SEPARATOR . $factoryStub)
        );

        $this->filesystem->put($this->modelParser->classPath(), $modelContents);
        $this->filesystem->put($this->replaceFactoryPath('classPath'), $factoryContents);
    }

    private function replaceFactoryPath($method)
    {
        return Str::replaceFirst(
            'app' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Factories',
            'database' . DIRECTORY_SEPARATOR . 'factories',
            $this->factoryParser->$method()
        );
    }
}
