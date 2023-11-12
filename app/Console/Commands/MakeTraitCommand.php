<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeTraitCommand extends Command
{
    /**
     * argumentName
     *
     * @var string
     */
    public $argumentName = 'trait';

    /**
     * Name and signiture of Command.
     * name
     * @var string
     */
    protected $name = 'make:trait';

    /**
     * command description.
     * description
     * @var string
     */
    protected $description = 'create a new trait';

    /**
     * Get Command argumant EX : HasAuth
     * getArguments
     *
     * @return void
     */
    protected function getArguments()
    {
        return [
            ['trait', InputArgument::REQUIRED, 'The name of the trait'],
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
     * getTraitName
     *
     * @return void
     */
    private function getTraitName()
    {
        $trait = Str::studly($this->argument('trait'));
        return $trait;
    }

    /**
     * getDestinationFilePath
     *
     * @return string
     */
    protected function getDestinationFilePath()
    {
        return app_path() . "/Http/Traits" . '/' . $this->getTraitName() . '.php';
    }

    /**
     * getTraitNameWithoutNamespace
     *
     * @return void
     */
    private function getTraitNameWithoutNamespace()
    {
        return class_basename($this->getTraitName());
    }

    /**
     * getClassNamespace
     *
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        return "App\\Http\\Traits";
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
     * @return void
     */
    protected function getStubFilePath()
    {
        $stub = '/stubs/traits.stub';
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
            'CLASS'             => $this->getTraitNameWithoutNamespace()
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
        $this->info("Trait generated successfully! path : {$path}");

        //return 0;

    }
}
