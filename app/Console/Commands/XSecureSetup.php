<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class XSecureSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xsecure:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a secure secret for XSecure and update .env file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Check if XSECURITY_ENABLED is true
        if (config('app.xsecure_enabled') !== true) {
            $this->error('XSECURITY_ENABLED is not set to true. Secret and token cannot be generated.');
            exit(1);
        }

        // Generate the secret and token using openssl command
        $secret = trim(shell_exec('openssl rand -base64 32'));

        if (empty($secret)) {
            $this->error('Failed to generate secret or token using openssl command.');
            exit(1);
        }

        // Read the existing .env file
        $envFilePath = base_path('.env');
        $envContent = file_get_contents($envFilePath);

        // Replace the existing XSECURITY_SECRET value
        $envContent = preg_replace('/^XSECURITY_SECRET=(.*)/m', 'XSECURITY_SECRET=' . $secret, $envContent);

        // Update the .env file with the new values
        if (file_put_contents($envFilePath, $envContent) !== false) {
            // Print the generated secret and token on the screen
            $this->info('Generated secret: ' . $secret);
            $this->info('XSECURITY_SECRET key have been updated in the .env file.');
            exit(0);
        } else {
            $this->error('Failed to update .env file.');
            exit(1);
        }
    }
}
