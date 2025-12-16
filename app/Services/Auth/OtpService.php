<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class OtpService
{
    private const USER_OTP_PREFIX = 'OTP_';

    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {}

    /**
     * generate  phone verification code and store them in cache.
     *
     * @param  int    $time in minutes
     * @return string code
     */
    public function generateOtpCode(User $user, $time)
    {
        // forget existing otp from cache
        Cache::forget(self::USER_OTP_PREFIX . $user->id);

        // generate code
        $length = config('auth.login.otp.length', 6);
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        $code = mt_rand($min, $max);

        // put them in cache
        Cache::put(self::USER_OTP_PREFIX . $user->id, $code, now()->addMinutes($time));
        $user->last_otp = $code;
        $user->save();

        // return generated code
        return $code;
    }

    /**
     * check if the OTP is expired from cache or not
     *
     * @return bool
     */
    public function isOtpExpired(User $user)
    {
        $cachedCode = Cache::get(self::USER_OTP_PREFIX . $user->id);

        return (bool) (! $cachedCode);
    }

    /**
     * check if the OTP is correct
     *
     * @param  string $code
     * @return bool
     */
    public function isCorrectOtp(User $user, $code)
    {
        $cachedCode = Cache::get(self::USER_OTP_PREFIX . $user->id);

        return ! ($code != $cachedCode);
    }

    /**
     * Clear the OTP from cache
     *
     * @return void
     */
    public function clearOtp(User $user)
    {
        Cache::forget(self::USER_OTP_PREFIX . $user->id);
    }

    /**
     * send Otp to a user
     *
     * @return void
     */
    public function sendOtp(User $user)
    {
        $phone = $user->phone;
        $expiryMinutes = config('auth.login.otp.expiry_minutes', 5);
        $otp = $this->generateOtpCode($user, $expiryMinutes);
        $body = "{$otp} is your OTP";
        // TODO: fire notification even like SMS,EMAIL etc.
    }
}
