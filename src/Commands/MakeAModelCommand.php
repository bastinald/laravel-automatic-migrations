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
            $this->line('<comment>Model exists:</comment> ' . $this->modelParser->relativeClassPath());
            $this->warn('Use the <info>--force</info> to overwrite it.');

            return;
        }

        $this->deleteUserMigrations();
        $this->makeStubs();

        $this->line('<info>Model created:</info> ' . $this->modelParser->relativeClassPath());
        $this->line('<info>Factory created:</info> ' . $this->factoryPath('relativeClassPath'));
    }

    private function deleteUserMigrations()
    {
        if ($this->modelParser->className() == 'User') {
            $names = ['create_users_table', 'add_timezone_column_to_users_table'];

            foreach ($this->filesystem->allFiles(database_path('migrations')) as $file) {
                if (Str::contains($file, $names)) {
                    $path = 'database/migrations/' . $file->getRelativePathname();

                    $this->filesystem->delete($file);

                    $this->line('<info>Migration deleted:</info> ' . $path);
                }
            }
        }
    }

    private function makeStubs()
    {
        $stubs = [
            $this->modelParser->classPath() =>
                $this->modelParser->className() == 'User' ? 'UserModel.php' : 'Model.php',
            $this->factoryPath('classPath') =>
                $this->modelParser->className() == 'User' ? 'UserFactory.php' : 'Factory.php',
        ];

        $replaces = [
            'DummyFactoryClass' => $this->factoryParser->className(),
            'DummyFactoryNamespace' => $this->factoryParser->classNamespace(),
            'DummyModelClass' => $this->modelParser->className(),
            'DummyModelNamespace' => $this->modelParser->classNamespace(),
        ];

        foreach ($stubs as $path => $stub) {
            $contents = Str::replace(
                array_keys($replaces),
                $replaces,
                $this->filesystem->get(config('laravel-automatic-migrations.stub_path') . '/' . $stub)
            );

            $this->filesystem->ensureDirectoryExists(dirname($path));
            $this->filesystem->put($path, $contents);
        }
    }

    private function factoryPath($method)
    {
        return Str::replaceFirst(
            'app/Database/Factories',
            'database/factories',
            $this->factoryParser->$method()
        );
    }
}
