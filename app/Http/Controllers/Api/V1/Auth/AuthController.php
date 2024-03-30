<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Services\Auth\AuthService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        protected AuthService $authService,
    ) {
    }


    /**
     * log the user in after verifying the otp
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)
            ->with('roles.permissions')
            ->first();
        $authData = $this->authService->login(
            $user,
            $request->password,
            $request->device,
        );
        $data = [
            'user' => UserResource::make($authData['user']),
            'token' => $authData['token'],
        ];
        return $this->sendSuccessResponse(
            $data,
            __('messages.login.success'),
            Response::HTTP_OK
        );
    }

    public function logout(LogoutRequest $request)
    {
        $this->authService->logout($request->device);
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
