<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRouteFileCommand extends Command
{
    protected $signature = 'make:route {name}';
    protected $description = 'Create a new route file for a given entity';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create view directory if not exists.
     */
    public function createDir($path)
    {
        $dir = dirname($path);

        if (! file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $snakeName = Str::snake($name);
        // $pluralName = Str::plural(Str::snake($name));
        $controller = "{$name}Controller";
        $controllerPath = "Api\V1\\{$name}\\{$name}Controller";
        $path = base_path("routes/api/v1/{$name}/{$name}Routes.php");

        $this->createDir($path);

        if (File::exists($path)) {
            $this->error("File {$path} already exists!");

            return 1;
        }

        $stub = $this->getStub();
        $content = str_replace(
            ['$CONTROLLER$', '$NAME$', '$CONTROLLER_PATH$'],
            [$controller, $snakeName, $controllerPath],
            $stub
        );

        File::put($path, $content);

        $this->info("Route file created successfully! Path: {$path}");

        return 0;
    }

    /**
     * getStubFilePath
     *
     * @return string
     */
    protected function getStubFilePath()
    {
        $stub = '/stubs/routes.stub';

        return $stub;
    }

    protected function getStub()
    {
        return file_get_contents(__DIR__ . $this->getStubFilePath());

    }
}
