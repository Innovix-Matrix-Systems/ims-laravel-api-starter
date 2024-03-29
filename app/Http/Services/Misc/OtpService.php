<?php

namespace App\Http\Services\Misc;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class OtpService
{
    private const USER_OTP_PREFIX = "OTP_";

    /**
     * @var SmsService
     */
    protected $smsService;

    /**
      * __construct
      *
       * @return void
      */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * generate  phone verification code and store them in cache.
     *
     * @param  \App\Models\User
     * @param  number $time in minutes
     * @return string code
     */
    public function generateOtpCode(User $user, $time)
    {
        //forget existing otp from cache
        Cache::forget(self::USER_OTP_PREFIX . $user->id);
        //generate code
        $code =  mt_rand(100000, 999999);
        //put them in cache
        Cache::put(self::USER_OTP_PREFIX . $user->id, $code, now()->addMinutes($time));
        $user->last_otp = $code;
        $user->save();
        //return generated code
        return $code;
    }


    /**
     * check if the OTP is expired from cache or not
     *
     * @param  \App\Models\User
     * @return boolean
     */
    public function isOtpExpired(User $user)
    {
        $cachedCode = Cache::get(self::USER_OTP_PREFIX . $user->id);
        if (!$cachedCode) {
            return true;
        }
        return false;
    }

    /**
     * check if the OTP is correct
     *
     * @param  \App\Models\User
     * @param  string  $code
     * @return boolean
     */
    public function isCorrectOtp(User $user, $code)
    {
        $cachedCode = Cache::get(self::USER_OTP_PREFIX . $user->id);
        if ($code != $cachedCode) {
            return false;
        }
        return true;
    }

    /**
     * send Otp to a user
     *
     * @param  \App\Models\User
     * @return void
     */
    public function sendOtp(User $user)
    {
        $phone = $user->phone;
        $otp = $this->generateOtpCode($user, 5);
        $body = "{$otp} is your OTP";
        $this->smsService->sendSms($phone, $body);
    }

}
