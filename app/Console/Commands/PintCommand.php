<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class PintCommand extends Command
{
    protected $signature = 'pint {--test : Run Pint in test mode without fixing files}';

    protected $description = 'Run Laravel Pint to fix code style issues';

    public function handle()
    {
        $command = ['php', 'vendor/bin/pint'];

        if ($this->option('test')) {
            $command[] = '--test';
        }

        $process = new Process($command);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        return $process->isSuccessful() ? 0 : 1;
    }
}
