<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $autToken;
    public $to;
    public $body;
    /**
     * Create a new job instance.
     */
    public function __construct($authToken, $to, $body)
    {
        $this->autToken = $authToken;
        $this->to = $to;
        $this->body = $body;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //implement logic here
    }
}
