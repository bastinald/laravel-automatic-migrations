<?php

namespace Bastinald\LaravelAutomaticMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Livewire\Commands\ComponentParser;

class MakeAModelCommand extends Command
{
    protected $signature = 'make:amodel {class} {--force}';
    private $filesystem;
    private $modelParser;

    public function handle()
    {
        $this->filesystem = new Filesystem;

        $this->modelParser = new ComponentParser(
            is_dir(app_path('Models')) ? 'App\\Models' : 'App',
            config('livewire.view_path'),
            $this->argument('class')
        );

        if ($this->filesystem->exists($this->modelParser->classPath()) && !$this->option('force')) {
            $this->warn('Model exists: <info>' . $this->modelParser->relativeClassPath() . '</info>');
            $this->warn('Use the <info>--force</info> to overwrite it.');

            return;
        }

        $this->deleteUserMigrations();
        $this->makeStub();
        $this->makeFactory();

        $this->warn('Model made: <info>' . $this->modelParser->relativeClassPath() . '</info>');
    }

    private function deleteUserMigrations()
    {
        if ($this->modelParser->className() == 'User') {
            $path = 'database ' . DIRECTORY_SEPARATOR . 'migrations';
            $names = ['create_users_table', 'add_timezone_column_to_users_table'];

            foreach ($this->filesystem->allFiles(base_path($path)) as $file) {
                if (Str::contains($file, $names)) {
                    $this->filesystem->delete($file);

                    $this->warn('File deleted: <info>' . $path . DIRECTORY_SEPARATOR . $file->getRelativePathname() . '</info>');
                }
            }
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
            $this->filesystem->get(config('laravel-automatic-migrations.stub_path') . DIRECTORY_SEPARATOR . $stub)
        );

        $this->filesystem->put($this->modelParser->classPath(), $contents);
    }

    private function makeFactory()
    {
        Artisan::call('make:afactory', [
            'class' => $this->modelParser->className() . 'Factory',
            '--force' => $this->option('force'),
        ], $this->getOutput());
    }
}
