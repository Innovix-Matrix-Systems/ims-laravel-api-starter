<?php

namespace App\Services\Auth;

use App\Enums\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Models\User;
use App\Traits\RateLimiterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    use RateLimiterTrait;

    public const AUTH_ERROR_GENERAL = 'AUTH_GENERAL';
    public const AUTH_ERROR_UNVERIFIED = 'ACCOUNT_UNVERIFIED';
    public const AUTH_ERROR_INACTIVE = 'ACCOUNT_DEACTIVATED';
    public const AUTH_ERROR_INCORRECT_PASSWORD = 'INVALID_PASSWORD';
    public const AUTH_ERROR_OTP_EXPIRED = 'OTP_EXPIRED';
    public const AUTH_ERROR_INCORRECT_OTP = 'OTP_INVALID';
    public const AUTH_ERROR_LOCKOUT = 'ACCOUNT_LOCKED';
    public const AUTH_SUCCESS_CODE = 'AUTH_SUCCESS';
    public const AUTH_OTP_REQUIRED = 'OTP_REQUIRED';

    private const USER_TOKEN_PREFIX = 'user_';

    public function __construct(
        protected OtpService $otpService,
    ) {}

    /**
     * Log the user into the application
     *
     *
     * @throws ApiException
     */
    public function login(User $user, string $password, string $device = ''): array
    {
        $request = request();

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse();
        }

        // Verify user status (Active, Verified, etc.) based on config
        $this->verifyBeforeLogin($user);

        // Verify Password
        if (! $this->isUserPasswordMatched($user, $password)) {
            $this->incrementLoginAttempts($request);
            $this->throwLoginError(self::AUTH_ERROR_INCORRECT_PASSWORD);
        }

        // Verify OTP if enabled
        if ($this->shouldVerifyOtp()) {
            $this->otpService->sendOtp($user);

            return [
                'user' => $user,
                'token' => null,
                'status' => self::AUTH_OTP_REQUIRED,
                'message' => __('messages.otp.sent'),
            ];
        }

        // Success
        $token = $user->createToken($this->generateTokenKey($user->id, $device) . $user->id)->plainTextToken;
        $this->clearLoginAttempts($request);
        $this->authenticated($user, $device);

        return [
            'user' => $user,
            'token' => $token,
            'status' => self::AUTH_SUCCESS_CODE,
            'message' => __('messages.login.success'),
        ];
    }

    /**
     * Verify OTP and log the user into the application
     *
     *
     * @throws ApiException
     */
    public function verifyOtpAndLogin(User $user, string $otp, string $device = ''): array
    {
        $request = request();

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse();
        }

        // Verify user status (Active, Verified, etc.) based on config
        $this->verifyBeforeLogin($user);

        // Verify OTP
        if (! $this->otpService->isCorrectOtp($user, $otp)) {
            $this->incrementLoginAttempts($request);
            $this->throwLoginError(self::AUTH_ERROR_INCORRECT_OTP);
        }

        if ($this->otpService->isOtpExpired($user)) {
            $this->incrementLoginAttempts($request);
            $this->throwLoginError(self::AUTH_ERROR_OTP_EXPIRED);
        }

        $this->otpService->clearOtp($user);

        // Success
        $token = $user->createToken($this->generateTokenKey($user->id, $device) . $user->id)->plainTextToken;
        $this->clearLoginAttempts($request);
        $this->authenticated($user, $device);

        return [
            'user' => $user,
            'token' => $token,
            'status' => self::AUTH_SUCCESS_CODE,
            'message' => __('messages.login.success'),
        ];
    }

    /** Check if the provided password matches the user's current password */
    public function isUserPasswordMatched(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    public function logout(string $device): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $deviceTokenKey = $this->generateTokenKey($user->id, $device) . $user->id;
        $user->tokens()
            ->where('tokenable_id', $user->id)
            ->where('tokenable_type', User::class)
            ->where('name', $deviceTokenKey)
            ->delete();
    }

    /**
     * Verify the user before attempting to log in based on configuration.
     *
     * @throws ApiException
     */
    protected function verifyBeforeLogin(User $user): void
    {
        $config = config('auth.login.verification', []);

        if (($config['check_is_active'] ?? true) && $user->is_active == false) {
            $this->throwLoginError(self::AUTH_ERROR_INACTIVE);
        }

        if (($config['check_email_verified'] ?? true) && ! $user->email_verified_at) {
            $this->throwLoginError(self::AUTH_ERROR_UNVERIFIED);
        }

        if (($config['check_phone_verified'] ?? false) && ! $user->phone_verified_at) {
            $this->throwLoginError(self::AUTH_ERROR_UNVERIFIED);
        }
    }

    protected function shouldVerifyOtp(): bool
    {
        return config('auth.login.otp.enabled', false);
    }

    /** The user has been authenticated. */
    protected function authenticated(User $user, string $device): void
    {
        $user->last_login_at = now();
        $user->save();
    }

    /** Clear the login locks for the given user credentials. */
    protected function clearLoginAttempts(Request $request): void
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /** Determine if the user has too many failed login attempts. */
    protected function hasTooManyLoginAttempts(Request $request): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            config('auth.login.max_attempts', 5)
        );
    }

    /** Increment the login attempts for the user. */
    protected function incrementLoginAttempts(Request $request): void
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            config('auth.login.decay_minutes', 1) * 60
        );
    }

    /**
     * Send lockout response to the user
     *
     * @throws ApiException
     */
    protected function sendLockoutResponse(): void
    {
        throw new ApiException(
            Response::HTTP_TOO_MANY_REQUESTS,
            ApiErrorCode::RATE_LIMIT_EXCEEDED->value,
            __('messages.login.lockout'),
            __('messages.login.fail.general')
        );
    }

    /**
     * Throw a login error exception
     *
     * @throws ApiException
     */
    protected function throwLoginError(string $errorCode): void
    {
        $responseCode = Response::HTTP_BAD_REQUEST;

        $errorMessage = match ($errorCode) {
            self::AUTH_ERROR_INACTIVE => __('messages.login.inactive'),
            self::AUTH_ERROR_UNVERIFIED => __('messages.login.unverified'),
            self::AUTH_ERROR_INCORRECT_PASSWORD => __('messages.login.invalid.password'),
            self::AUTH_ERROR_INCORRECT_OTP => __('messages.login.invalid.otp'),
            self::AUTH_ERROR_OTP_EXPIRED => __('messages.login.expired.otp'),
            self::AUTH_ERROR_LOCKOUT => __('messages.login.lockout'),
            default => __('messages.login.fail.general'),
        };

        $responseCode = match ($errorCode) {
            self::AUTH_ERROR_INCORRECT_PASSWORD => Response::HTTP_BAD_REQUEST,
            self::AUTH_ERROR_UNVERIFIED => Response::HTTP_FORBIDDEN,
            self::AUTH_ERROR_INACTIVE => Response::HTTP_FORBIDDEN,
            self::AUTH_ERROR_LOCKOUT => Response::HTTP_LOCKED,
            self::AUTH_ERROR_OTP_EXPIRED => Response::HTTP_BAD_REQUEST,
            self::AUTH_ERROR_INCORRECT_OTP => Response::HTTP_BAD_REQUEST,
            default => Response::HTTP_BAD_REQUEST,
        };

        throw new ApiException(
            $responseCode,
            $errorCode,
            $errorMessage,
            __('messages.login.fail.general')
        );
    }

    /** Generate auth token key for a user */
    private function generateTokenKey(string $userId, string $device): string
    {
        return self::USER_TOKEN_PREFIX . $userId . '_' . $device;
    }
}
