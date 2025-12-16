<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeRepositoryCommand extends Command
{
    public $argumentName = 'repository';

    protected $name = 'make:repo';

    protected $description = 'create a new repository class';

    public function __construct()
    {
        parent::__construct();
    }

    public function getDefaultNamespace(): string
    {
        return 'App\\Repositories';
    }

    public function getClass()
    {
        return class_basename($this->argument($this->argumentName));
    }

    public function getClassNamespace()
    {
        $extra = str_replace($this->getClass(), '', $this->argument($this->argumentName));
        $extra = str_replace('/', '\\', $extra);
        $namespace = $this->getDefaultNamespace() . '\\' . $extra;
        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }

    public function createDir($path)
    {
        $dir = dirname($path);
        if (! file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    public function handle()
    {
        $path = str_replace('\\', '/', $this->getDestinationFilePath());
        $fileContents = $this->getTemplateContents();

        $this->createDir($path);

        if (File::exists($path)) {
            $this->error("File {$path} already exists!");

            return 1;
        }

        File::put($path, $fileContents);
        $this->info("Repository generated successfully! path : {$path}");

        return 0;
    }

    protected function getArguments()
    {
        return [
            ['repository', InputArgument::REQUIRED, 'The name of the repository class.'],
        ];
    }

    protected function getDestinationFilePath()
    {
        return app_path() . '/Repositories' . '/' . $this->getRepositoryName() . '.php';
    }

    protected function getStubFilePath()
    {
        $stub = '/stubs/repository.stub';

        return $stub;
    }

    protected function getTemplateContents()
    {
        $fileTemplate = file_get_contents(__DIR__ . $this->getStubFilePath());

        $replaceOptions = [
            'CLASS_NAMESPACE' => $this->getClassNamespace(),
            'CLASS' => $this->getRepositoryNameWithoutNamespace(),
        ];

        foreach ($replaceOptions as $search => $replace) {
            $fileTemplate = str_replace('$' . strtoupper($search) . '$', $replace, $fileTemplate);
        }

        return $fileTemplate;
    }

    private function getRepositoryName()
    {
        $name = Str::studly($this->argument('repository'));

        if (Str::contains(strtolower($name), 'repository') === false) {
            $name .= 'Repository';
        }

        return $name;
    }

    private function getRepositoryNameWithoutNamespace()
    {
        return class_basename($this->getRepositoryName());
    }
}
