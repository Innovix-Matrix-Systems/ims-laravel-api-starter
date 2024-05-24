<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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

        $this->info("Creating CRUD for {$name}");

        $tasks = [
            'migration' => ['make:migration', ['name' => "create_{$name}_table"]],
            'model' => ['make:model', ['name' => $name]],
            'controller' => [
                'make:controller',
                [
                    'name' => "Api/V1/{$name}/{$name}Controller",
                    '--api' => true,
                    '--model' => $name
                ]
            ],
            'request' => ['make:request', ['name' => "{$name}/{$name}InsertUpdateRequest"]],
            'resource' => ['make:resource', ['name' => "{$name}/{$name}Resource"]],
            'service' => ['make:service', ['service' => "{$name}/{$name}Service"]],
            'dto' => ['make:dto', ['dto' => "{$name}DTO"]],
            'route' => ['make:route', ['name' => $name]]
        ];

        foreach ($tasks as $taskName => $task) {
            [$command, $arguments] = $task;
            if ($this->callArtisanCommand($taskName, $command, $arguments)) {
                return;
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
