<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeDTOCommand extends Command
{
    /**
     * argumentName
     *
     * @var string
     */
    public $argumentName = 'dto';

    /**
     * Name and signiture of Command.
     * name
     * @var string
     */
    protected $name = 'make:dto';

    /**
     * command description.
     * description
     * @var string
     */
    protected $description = 'create a new DTO';

    /**
     * Get Command argumant EX : HasAuth
     * getArguments
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['dto', InputArgument::REQUIRED, 'The name of the DTO'],
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
     * getDTOName
     *
     * @return string
     */
    private function getDTOName()
    {
        $dto = Str::studly($this->argument('dto'));
        return $dto;
    }

    /**
     * getDestinationFilePath
     *
     * @return string
     */
    protected function getDestinationFilePath()
    {
        return app_path() . "/Http/DTOs" . '/' . $this->getDTOName() . '.php';
    }

    /**
     * getDTONameWithoutNamespace
     *
     * @return string
     */
    private function getDTONameWithoutNamespace()
    {
        return class_basename($this->getDTOName());
    }

    /**
     * getClassNamespace
     *
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        return "App\\Http\\DTOs";
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
     * getStubFilePath
     *
     * @return string
     */
    protected function getStubFilePath()
    {
        $stub = '/stubs/dto.stub';
        return $stub;
    }

    /**
     * getTemplateContents
     *
     * @return array|bool|string
     */
    protected function getTemplateContents()
    {
        $fileTemplate = file_get_contents(__DIR__ . $this->getStubFilePath());

        $replaceOptions = [
            'CLASS_NAMESPACE'   => $this->getClassNamespace(),
            'CLASS'             => $this->getDTONameWithoutNamespace()
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
            exit(1);
        }

        File::put($path, $fileContents);
        $this->info("DTO generated successfully! path : {$path}");
        exit(0);

        //return 0;

    }
}
