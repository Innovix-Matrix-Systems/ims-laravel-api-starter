<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeMapperCommand extends Command
{
    protected $name = 'make:mapper';

    protected $description = 'Create a new mapper class';

    protected string $argumentName = 'mapper';

    public function __construct()
    {
        parent::__construct();
    }

    public function getDefaultNamespace(): string
    {
        return 'App\\Http\\Mappers';
    }

    public function getClass(): string
    {
        return class_basename($this->argument($this->argumentName));
    }

    public function getClassNamespace(): string
    {
        $extra = str_replace($this->getClass(), '', $this->argument($this->argumentName));
        $extra = str_replace('/', '\\', $extra);
        $namespace = $this->getDefaultNamespace() . '\\' . $extra;

        return trim(str_replace('/', '\\', $namespace), '\\');
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
        $this->info("Mapper generated successfully! path : {$path}");

        return 0;
    }

    protected function getArguments(): array
    {
        return [
            [$this->argumentName, InputArgument::REQUIRED, 'The name of the mapper class.'],
        ];
    }

    protected function getDestinationFilePath(): string
    {
        return app_path() . '/Http/Mappers/' . $this->getMapperName() . '.php';
    }

    protected function getStubFilePath(): string
    {
        return '/stubs/mappers.stub';
    }

    protected function getTemplateContents(): string
    {
        $template = file_get_contents(__DIR__ . $this->getStubFilePath());

        $replaceOptions = [
            'CLASS_NAMESPACE' => $this->getClassNamespace(),
            'CLASS' => $this->getMapperNameWithoutNamespace(),
        ];

        foreach ($replaceOptions as $search => $replace) {
            $template = str_replace('$' . strtoupper($search) . '$', $replace, $template);
        }

        return $template;
    }

    protected function getMapperName(): string
    {
        $mapper = Str::studly($this->argument($this->argumentName));

        if (! Str::endsWith(strtolower($mapper), 'mapper')) {
            $mapper .= 'Mapper';
        }

        return $mapper;
    }

    protected function getMapperNameWithoutNamespace(): string
    {
        return class_basename($this->getMapperName());
    }

    protected function createDir(string $path)
    {
        $dir = dirname($path);
        if (! file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
