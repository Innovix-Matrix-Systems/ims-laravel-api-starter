<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class GenerateCrudStarter extends Command
{
    protected $signature = 'make:crud {name}';
    protected $description = 'Create a new CRUD starter setup for a given entity';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name');
        $snakeName = Str::snake($name);

        $this->info("Creating CRUD for {$name}");

        $tasks = [
            'migration' => ['make:migration', ['name' => "create_{$snakeName}_table"]],
            'model' => ['make:model', ['name' => $name]],
            'controller' => [
                'make:controller',
                [
                    'name' => "Api/V1/{$name}/{$name}Controller",
                    '--api' => true,
                    '--model' => $name,
                ],
            ],
            'request' => ['make:request', ['name' => "{$name}/{$name}InsertUpdateRequest"]],
            'resource' => ['make:resource', ['name' => "{$name}/{$name}Resource"]],
            'policy' => ['make:policy', ['name' => "{$name}Policy", '--model' => $name]],
            'service' => ['make:service', ['service' => "{$name}/{$name}Service"]],
            'dto' => ['make:dto', ['dto' => "{$name}DTO"]],
            'route' => ['make:route', ['name' => $name]],
            'feature_test' => ['make:test', ['name' => "{$name}FeatureTest"]],
            'unit_test' => ['make:test', ['name' => "{$name}UnitTest", '--unit' => true]],
        ];

        foreach ($tasks as $taskName => $task) {
            [$command, $arguments] = $task;
            if ($taskName === 'controller') {
                if (isset($arguments['--api'])) {
                    $this->callArtisanCommand('API Controller', 'make:controller', $arguments);
                } else {
                    unset($arguments['--api']); // Remove --api option
                    $this->callArtisanCommand('Controller', 'make:controller', $arguments);
                }
            } else {
                $this->callArtisanCommand($taskName, $command, $arguments);
            }
        }

        $this->info('CRUD starter created successfully!');
    }

    private function callArtisanCommand($taskName, $command, $arguments)
    {
        try {
            $exitCode = Artisan::call($command, $arguments);
            $this->line(Artisan::output());

            return $exitCode !== 0;
        } catch (\Exception $e) {
            $this->error("Error creating {$taskName}: {$e->getMessage()}");

            return true;
        }
    }
}
