<?php

namespace App\Http\Services\Misc;

use App\Jobs\SendSmsJob;

class SmsService
{
    /**
     * @var $token
     */
    protected $token;
    /**
      * __construct
      *
       * @return void
      */
    public function __construct()
    {
        $this->token = config('envs.sms_token');
    }


    public function sendSms($to, $body)
    {
        dispatch(new SendSmsJob($this->token, $to, $body));
    }

}
