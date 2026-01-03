<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Authentication
 *
 * APIs for user authentication, login, and logout
 */
class AuthController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        protected AuthService $authService,
    ) {}

    /**
     * User login
     *
     * Authenticate user with email and password, return user data and access token.
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "message": "Login is successful.",
     *   "status": "AUTH_SUCCESS",
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "Super Admin",
     *       "email": "superadmin@ims.com",
     *       "email_verified_at": "2025-12-06T10:44:38.000000Z",
     *       "phone": "01700000000",
     *       "is_active": true,
     *       "created_at": "2025-12-06T10:44:38.000000Z",
     *       "updated_at": "2025-12-07T06:54:31.000000Z",
     *       "last_login_at": "2025-12-07T06:54:31.000000Z",
     *       "last_active_device": "mobile_app",
     *       "roles": [
     *         { "id": 1, "name": "Super-Admin" }
     *       ],
     *       "is_deleted": false,
     *       "roleNames": [ "Super-Admin" ],
     *       "branch_name": "Main Branch",
     *       "photo": null
     *     },
     *     "token": "5|kbBO03JKaZDnwQC1S7Qq0CmvSk3qIjsrXuccicUs9ecf3371"
     *   }
     * }
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::select([
            'id',
            'name',
            'email',
            'email_verified_at',
            'phone',
            'password',
            'is_active',
            'created_at',
            'updated_at',
        ])
            ->where('email', $request->email)
            ->with('roles', 'media')
            ->first();
        $authData = $this->authService->login(
            $user,
            $request->password,
            $request->device,
        );

        $data = [
            'user' => UserResource::make($authData['user']),
            'status' => $authData['status'],
            'message' => $authData['message'],
            'token' => $authData['token'],
        ];

        return $this->respond($data, Response::HTTP_OK);
    }

    /**
     * Verify OTP
     *
     * Verify the OTP sent to the user and return access token.
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "message": "Login is successful.",
     *   "status": "AUTH_SUCCESS",
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "Super Admin",
     *       "email": "superadmin@ims.com",
     *       "email_verified_at": "2025-12-06T10:44:38.000000Z",
     *       "phone": "01700000000",
     *       "is_active": true,
     *       "created_at": "2025-12-06T10:44:38.000000Z",
     *       "updated_at": "2025-12-07T06:54:31.000000Z",
     *       "last_login_at": "2025-12-07T06:54:31.000000Z",
     *       "last_active_device": "mobile_app",
     *       "roles": [
     *         { "id": 1, "name": "Super-Admin" }
     *       ],
     *       "is_deleted": false,
     *       "roleNames": [ "Super-Admin" ],
     *       "branch_name": "Main Branch",
     *       "photo": null
     *     },
     *     "token": "5|kbBO03JKaZDnwQC1S7Qq0CmvSk3qIjsrXuccicUs9ecf3371"
     *   }
     * }
     *
     * @return JsonResponse
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        $authData = $this->authService->verifyOtpAndLogin(
            $user,
            $request->otp,
            $request->device
        );

        $data = [
            'user' => UserResource::make($authData['user']),
            'status' => $authData['status'],
            'message' => $authData['message'],
            'token' => $authData['token'],
        ];

        return $this->respond($data, Response::HTTP_OK);
    }

    /**
     * User logout
     *
     * Logout user from specific device and revoke access token.
     */
    public function logout(LogoutRequest $request)
    {
        $this->authService->logout($request->device);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
