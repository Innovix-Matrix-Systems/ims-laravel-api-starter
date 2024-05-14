<?php

namespace App\Http\Services\Auth;

use App\Enums\UserStatus;
use App\Exceptions\Auth\LoginErrorException;
use App\Http\Services\Misc\OtpService;
use App\Models\User;
use App\Traits\RateLimitterTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    use RateLimitterTrait;
    const USER_TOKEN_PREFIX = 'user_';
    const MAX_LOGIN_ATTEMPTS = 5;
    const MAX_DECAY_MINUTES = 1;
    const AUTH_ERROR_GENERAL = 999;
    const AUTH_ERROR_UNVERIFIED = 1000;
    const AUTH_ERROR_DEACTIVE = 1001;
    const AUTH_ERROR_INCORRECT_PASSWORD = 1002;
    const AUTH_ERROR_OTP_EXPIRED = 1003;
    const AUTH_ERROR_INCORRECT_OTP = 1004;
    const AUTH_ERROR_LOCKOUT = 1005;
    const AUTH_SUCCESS_CODE = 10;
    const AUTH_OTP_SUCCESS_CODE = 11;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        protected OtpService $otpService,
    ) {
    }

    /**
     * Verify the user before Attempting to log the user into the application.
     *
     * @param  \App\Models\User $user
     * @return string           error code
     */
    protected function verifyBeforeLogin(User $user)
    {
        if ($user->is_active == UserStatus::DEACTIVE->value) {
            return self::AUTH_ERROR_DEACTIVE;
        }
        if (!$user->email_verified_at) {
            return self::AUTH_ERROR_UNVERIFIED;
        }

        return self::AUTH_SUCCESS_CODE;
    }

    /**
     * check if the provided password is matched with the user current password
     *
     * @param  \App\Models\User $user
     * @param  string           $password
     * @return boolean
     */
    public function isUserPasswordMatched(User $user, $password)
    {
        if (Hash::check($password, $user->password)) {
            return true;
        }
        return false;
    }

    /**
     * The user has been authenticated.
     *
     * @param  \App\Models\User $user
     * @param  string           $device
     * @return void
     */
    protected function authenticated(User $user, $device)
    {
        $user->last_login_at = now();
        $user->last_active_device = $device;
        $user->save();
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            self::MAX_LOGIN_ATTEMPTS
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            self::MAX_DECAY_MINUTES * 60
        );
    }
    /**
     * Log the user into the application
     *
     * @param  \App\Models\User $user
     * @param  string           $password
     * @param  string           $device
     * @return array
     * @throws \App\Exceptions\Auth\LoginErrorException;
     */
    public function login(User $user, $password, $device = "")
    {
        if ($this->hasTooManyLoginAttempts(request())) {
            $this->fireLockoutEvent(request());
            $this->sendLockoutResponse();
        }

        $authCode = $this->verifyBeforeLogin($user);

        if ($authCode == self::AUTH_ERROR_DEACTIVE) {
            //send Deactivate Error Response
            $this->sendFailedLoginResponse(self::AUTH_ERROR_DEACTIVE);
        }

        if ($authCode == self::AUTH_ERROR_UNVERIFIED) {
            //send  unverified Error Response
            $this->sendFailedLoginResponse(self::AUTH_ERROR_UNVERIFIED);
        }

        if (!$this->isUserPasswordMatched($user, $password)) {
            //send failed login response
            $this->incrementLoginAttempts(request());
            $this->sendFailedLoginResponse(self::AUTH_ERROR_INCORRECT_PASSWORD);
        }

        if ($authCode == self::AUTH_SUCCESS_CODE) {
            $token = $user->createToken($this->generateTokenKey($user->id, $device) . $user->id)->plainTextToken;
            $this->clearLoginAttempts(request());
            $this->authenticated($user, $device);

            return [
                'user'  => $user,
                'token' => $token,
            ];

        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts(request());
        $this->sendFailedLoginResponse(self::AUTH_ERROR_GENERAL);

    }

    public function logout(string $device)
    {
        $user = Auth::user();
        $deviceTokenKey = $this->generateTokenKey($user->id, $device) . $user->id;
        $user->tokens()
            ->where('tokenable_id', $user->id)
            ->where('tokenable_type', User::class)
            ->where('name', $deviceTokenKey)
            ->delete();
    }

    /**
     * generate auth token key for a user
     *
     * @param  string $userId
     * @param  string $device
     * @return string
     */
    private function generateTokenKey($userId, $device)
    {
        return self::USER_TOKEN_PREFIX . $userId . '_' . $device;
    }

    /**
     * send lockout response to the user
     *
     * @throws \App\Exceptions\Auth\LoginErrorException;
     */
    protected function sendLockoutResponse()
    {
        throw new LoginErrorException(
            Response::HTTP_BAD_REQUEST,
            self::AUTH_ERROR_LOCKOUT,
            __('messages.login.lockout')
        );
    }

    /**
     * send Failed login response
     * @param int $authErrorCode
     * @throws \App\Exceptions\Auth\LoginErrorException;
     */
    protected function sendFailedLoginResponse(int $authErrorCode)
    {
        $errorCode = self::AUTH_ERROR_GENERAL;
        $errorMessage = __('messages.login.general');
        $responseCode = Response::HTTP_BAD_REQUEST;
        switch ($authErrorCode) {
            case self::AUTH_ERROR_DEACTIVE:
                $errorCode = self::AUTH_ERROR_DEACTIVE;
                $errorMessage = __('messages.login.deactive');
                break;
            case self::AUTH_ERROR_UNVERIFIED:
                $errorCode = self::AUTH_ERROR_UNVERIFIED;
                $errorMessage = __('messages.login.unverified');
                break;
            case self::AUTH_ERROR_INCORRECT_PASSWORD:
                $errorCode = self::AUTH_ERROR_INCORRECT_PASSWORD;
                $errorMessage = __('messages.login.invalid.password');
                break;
            default:
                break;
        }
        throw new LoginErrorException(
            $responseCode,
            $errorCode,
            $errorMessage,
        );
    }
}
