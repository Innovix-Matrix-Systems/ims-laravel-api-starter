<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RunPhpCsFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csfixer:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run PHP CS Fixer to format code';

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
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Running PHP CS Fixer...');

        $process = new Process([
            'vendor/bin/php-cs-fixer',
            'fix',
            '--config=.php-cs-fixer.php',
        ]);

        // $process->run();
        $process->run(function ($type, $output) {
            $this->output->write($output);
            $this->output->write("\n");
        });

        if ($process->isSuccessful()) {
            $this->info('PHP CS Fixer has run successfully.');
            exit(0);
        } else {
            $this->error('An error occurred while running PHP CS Fixer.');
            $this->line($process->getErrorOutput());
            exit(1);
        }
    }
}
