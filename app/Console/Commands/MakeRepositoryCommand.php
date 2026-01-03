<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    public $argumentName = 'repository';

    protected $signature = 'make:repo {repository}';

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
        $interfacePath = str_replace('\\', '/', $this->getInterfaceDestinationFilePath());

        $fileContents = $this->getTemplateContents();
        $interfaceContents = $this->getInterfaceTemplateContents();

        $this->createDir($path);
        $this->createDir($interfacePath);

        if (File::exists($path)) {
            $this->error("File {$path} already exists!");

            return 1;
        }

        if (File::exists($interfacePath)) {
            $this->error("File {$interfacePath} already exists!");

            return 1;
        }

        File::put($path, $fileContents);
        File::put($interfacePath, $interfaceContents);

        $this->info("Repository generated successfully! path : {$path}");
        $this->info("Interface generated successfully! path : {$interfacePath}");

        $this->bindRepository();

        return 0;
    }

    protected function getInterfaceDestinationFilePath()
    {
        return app_path() . '/Repositories/Contracts/' . $this->getInterfaceName() . '.php';
    }

    protected function getInterfaceStubFilePath()
    {
        return '/stubs/repository_interface.stub';
    }

    protected function getInterfaceTemplateContents()
    {
        $fileTemplate = file_get_contents(__DIR__ . $this->getInterfaceStubFilePath());

        $replaceOptions = [
            'CLASS' => $this->getInterfaceName(),
        ];

        foreach ($replaceOptions as $search => $replace) {
            $fileTemplate = str_replace('$' . strtoupper($search) . '$', $replace, $fileTemplate);
        }

        return $fileTemplate;
    }

    protected function getTemplateContents()
    {
        $fileTemplate = file_get_contents(__DIR__ . $this->getStubFilePath());

        $replaceOptions = [
            'CLASS_NAMESPACE' => $this->getClassNamespace(),
            'CLASS' => $this->getRepositoryNameWithoutNamespace(),
            'INTERFACE' => $this->getInterfaceName(),
        ];

        foreach ($replaceOptions as $search => $replace) {
            $fileTemplate = str_replace('$' . strtoupper($search) . '$', $replace, $fileTemplate);
        }

        return $fileTemplate;
    }

    protected function getDestinationFilePath()
    {
        return app_path() . '/Repositories' . '/' . $this->getRepositoryName() . '.php';
    }

    protected function getStubFilePath()
    {
        return '/stubs/repository.stub';
    }

    private function getInterfaceName()
    {
        return $this->getRepositoryNameWithoutNamespace() . 'Interface';
    }

    private function bindRepository()
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');
        $providerContents = file_get_contents($providerPath);

        $interfaceClass = 'App\\Repositories\\Contracts\\' . $this->getInterfaceName();
        $repositoryClass = $this->getClassNamespace() . '\\' . $this->getRepositoryNameWithoutNamespace();

        $binding = "\$this->app->bind(\\{$interfaceClass}::class, \\{$repositoryClass}::class);";

        if (strpos($providerContents, $binding) === false) {
            $providerContents = str_replace(
                'public function register(): void' . PHP_EOL . '    {',
                'public function register(): void' . PHP_EOL . '    {' . PHP_EOL . '        ' . $binding,
                $providerContents
            );

            file_put_contents($providerPath, $providerContents);
            $this->info('Repository binding added to RepositoryServiceProvider.');
        }
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
