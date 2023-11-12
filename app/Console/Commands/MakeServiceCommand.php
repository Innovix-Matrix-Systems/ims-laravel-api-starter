<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeServiceCommand extends Command
{
    /**
     * argumentName
     *
     * @var string
     */
    public $argumentName = 'service';


    /**
     * Name and signiture of Command.
     * name
     * @var string
     */
    protected $name = 'make:service';


    /**
     * command description.
     * description
     * @var string
     */
    protected $description = 'create a new service class';



    /**
     * Get command agrumants - EX : UserService
     * getArguments
     *
     * @return void
     */
    protected function getArguments()
    {
        return [
            ['service', InputArgument::REQUIRED, 'The name of the service class.'],
        ];
    }


    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Return Service name as convention
     * getServiceName
     *
     * @return void
     */
    private function getServiceName()
    {
        $service = Str::studly($this->argument('service'));

        if (Str::contains(strtolower($service), 'service') === false) {
            $service .= 'Service';
        }

        return $service;
    }

    /**
     * Return destination path for class file publish
     * getDestinationFilePath
     *
     * @return string
     */
    protected function getDestinationFilePath()
    {
        return app_path() . "/Http/Services" . '/' . $this->getServiceName() . '.php';
    }


    /**
     * Return only service class name
     * getServiceNameWithoutNamespace
     *
     * @return void
     */
    private function getServiceNameWithoutNamespace()
    {
        return class_basename($this->getServiceName());
    }

    /**
     * Set Default Namespace
     * Override CommandGenerator class method
     * getDefaultNamespace
     *
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        return "App\\Http\\Services";
    }

    /**
    * Return a vaid class name
    * getClass
    *
     * @return string
    */
    public function getClass()
    {
        return class_basename($this->argument($this->argumentName));
    }


    /**
     * Generate class namespace dinamacally
     * getClassNamespace
     *
     * @return string
     */
    public function getClassNamespace()
    {
        $extra = str_replace($this->getClass(), '', $this->argument($this->argumentName));

        $extra = str_replace('/', '\\', $extra);

        $namespace =  $this->getDefaultNamespace();

        $namespace .= '\\' . $extra;

        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }


    /**
     * Return stub file path
     * getStubFilePath
     *
     * @return void
     */
    protected function getStubFilePath()
    {
        $stub = '/stubs/services.stub';

        return $stub;
    }

    /**
     * getTemplateContents
     *
     * @return void
     */
    protected function getTemplateContents()
    {
        $fileTemplate = file_get_contents(__DIR__ . $this->getStubFilePath());

        $replaceOptions = [
            'CLASS_NAMESPACE'   => $this->getClassNamespace(),
            'CLASS'             => $this->getServiceNameWithoutNamespace()
        ];

        foreach ($replaceOptions as $search => $replace) {
            $fileTemplate = str_replace('$' . strtoupper($search) . '$', $replace, $fileTemplate);
        }

        return $fileTemplate;
    }

    /**
     * Create view directory if not exists.
     *
     * @param $path
     */
    public function createDir($path)
    {
        $dir = dirname($path);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = str_replace('\\', '/', $this->getDestinationFilePath());

        $fileContents = $this->getTemplateContents();

        $this->createDir($path);

        if (File::exists($path)) {
            $this->error("File {$path} already exists!");
            return;
        }

        File::put($path, $fileContents);
        $this->info("Service generated successfully! path : {$path}");
    }
}
