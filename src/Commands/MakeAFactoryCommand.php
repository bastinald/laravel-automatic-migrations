<?php

namespace Bastinald\LaravelAutomaticMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Livewire\Commands\ComponentParser;

class MakeAFactoryCommand extends Command
{
    protected $signature = 'make:afactory {class} {--m|--model} {--force}';
    private $modelParser;
    private $factoryParser;

    public function handle()
    {
        $modelClass = Str::replaceLast('Factory', '', $this->argument('class'));

        $this->modelParser = new ComponentParser(
            is_dir(app_path('Models')) ? 'App\\Models' : 'App',
            config('livewire.view_path'),
            $modelClass
        );

        $this->factoryParser = new ComponentParser(
            'Database\\Factories',
            config('livewire.view_path'),
            $modelClass . 'Factory'
        );

        if (file_exists($this->replacePath('classPath')) && !$this->option('force')) {
            $this->warn('Factory exists: <info>' . $this->replacePath('relativeClassPath') . '</info>');
            $this->warn('Use the <info>--force</info> to overwrite it.');

            return;
        }

        $this->makeStub();

        if ($this->option('model')) {
            $this->makeModel();
        }
    }

    private function replacePath($method)
    {
        return Str::replaceFirst(
            'app/Database/Factories',
            'database/factories',
            $this->factoryParser->$method()
        );
    }

    private function makeStub()
    {
        $replaces = [
            'DummyFactoryClass' => $this->factoryParser->className(),
            'DummyFactoryNamespace' => $this->factoryParser->classNamespace(),
            'DummyModelClass' => $this->modelParser->className(),
            'DummyModelNamespace' => $this->modelParser->classNamespace(),
        ];
        $stub = $this->modelParser->className() == 'User' ? 'UserFactory.php' : 'Factory.php';

        $contents = str_replace(
            array_keys($replaces),
            $replaces,
            file_get_contents(config('laravel-automatic-migrations.stub_path') . '/' . $stub)
        );

        file_put_contents($this->replacePath('classPath'), $contents);

        $this->warn('Factory made: <info>' . $this->replacePath('relativeClassPath') . '</info>');
    }

    private function makeModel()
    {
        Artisan::call('make:amodel', [
            'class' => $this->modelParser->className(),
            '--force' => $this->option('force'),
        ], $this->getOutput());
    }
}
