<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeHelperCommand extends Command
{
    public $argumentName = 'helper';

    protected $name = 'make:helper';

    protected $description = 'create a new helper class';

    public function __construct()
    {
        parent::__construct();
    }

    public function getDefaultNamespace(): string
    {
        return 'App\\Helpers';
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
        $this->info("Helper generated successfully! path : {$path}");

        return 0;
    }

    protected function getArguments()
    {
        return [
            ['helper', InputArgument::REQUIRED, 'The name of the helper class.'],
        ];
    }

    protected function getDestinationFilePath()
    {
        return app_path() . '/Helpers' . '/' . $this->getHelperName() . '.php';
    }

    protected function getStubFilePath()
    {
        $stub = '/stubs/helper.stub';

        return $stub;
    }

    protected function getTemplateContents()
    {
        $fileTemplate = file_get_contents(__DIR__ . $this->getStubFilePath());

        $replaceOptions = [
            'CLASS_NAMESPACE' => $this->getClassNamespace(),
            'CLASS' => $this->getHelperNameWithoutNamespace(),
        ];

        foreach ($replaceOptions as $search => $replace) {
            $fileTemplate = str_replace('$' . strtoupper($search) . '$', $replace, $fileTemplate);
        }

        return $fileTemplate;
    }

    private function getHelperName()
    {
        $helper = Str::studly($this->argument('helper'));

        if (Str::contains(strtolower($helper), 'helper') === false) {
            $helper .= 'Helper';
        }

        return $helper;
    }

    private function getHelperNameWithoutNamespace()
    {
        return class_basename($this->getHelperName());
    }
}
