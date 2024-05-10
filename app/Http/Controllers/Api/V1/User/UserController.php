<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Enums\UserRoleID;
use App\Http\Controllers\Controller;
use App\Http\DTOs\UserDTO;
use App\Http\Requests\User\AdminAssignUserRoleRequest;
use App\Http\Requests\User\AdminUserPasswordUpdateRequest;
use App\Http\Requests\User\UserInsertUpdateRequest;
use App\Http\Requests\User\UserPasswordUpdateRequest;
use App\Http\Requests\User\UserProfileUpdateRequest;
use App\Http\Resources\Auth\UserResource;
use App\Http\Services\User\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        protected UserService $userService,
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);
        $searchFields = ['name', 'email', 'phone'];
        $selectFields = [
            'is_active' => $request->is_active,
        ];

        $UserQuery = User::with('roles');

        $users = $this->applyFilters(
            $request->search,
            $searchFields,
            $selectFields,
            $UserQuery,
        )
            ->orderBy($request->order_by ?? 'created_at', $request->order_direction ?? 'asc')
            ->paginate($request->per_page ?: 10, ['*'], 'page');

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserInsertUpdateRequest $request)
    {
        Gate::authorize('create', User::class);
        $roles = Role::whereIn('id', $request->roles)
        ->where('id', '!=', UserRoleID::SUPER_ADMIN_ID)
        ->get();
        $data = new UserDTO(
            null,
            $request->first_name,
            $request->last_name,
            $request->name,
            $request->email,
            $request->password,
            $request->phone,
            $request->designation,
            $request->address,
            $request->is_active,
            $roles,
        );
        $user = $this->userService->insertUserData($data);

        return $this->sendSuccessCollectionResponse(
            UserResource::make($user),
            __('http-statuses.201'),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id)->load('roles');
        Gate::authorize('view', $user);
        return $this->sendSuccessCollectionResponse(
            UserResource::make($user),
            __('http-statuses.200'),
            Response::HTTP_OK
        );

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserInsertUpdateRequest $request)
    {
        $user = User::findOrFail($request->id);
        Gate::authorize('update', $user);

        $data = new UserDTO(
            $user->id,
            $request->first_name,
            $request->last_name,
            $request->name,
            $request->email,
            null,
            $request->phone,
            $request->designation,
            $request->address,
            $request->is_active,
            null
        );

        $updatedUser = $this->userService->updateUserData($data, $user);

        return $this->sendSuccessCollectionResponse(
            UserResource::make($updatedUser),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        Gate::authorize('delete', $user);
        $user->delete();
        return $this->sendSuccessResponse(
            [],
            __('messages.delete.success'),
            Response::HTTP_NO_CONTENT
        );

    }

    /**
     * assign role to specified user
     */
    public function assignRole(AdminAssignUserRoleRequest $request)
    {
        $user = User::findOrFail($request->user_id)->load('roles');
        Gate::authorize('assignRole', $user);
        $roles = Role::whereIn('id', $request->roles)
        ->where('id', '!=', UserRoleID::SUPER_ADMIN_ID)
        ->get();
        $updatedUser = $this->userService->assignUserRole($user, $roles);

        return $this->sendSuccessCollectionResponse(
            UserResource::make($updatedUser),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }

    /**
     * Update password of specified user
     */
    public function changePassword(AdminUserPasswordUpdateRequest $request)
    {
        $user = User::findOrFail($request->user_id);
        Gate::authorize('update', $user);
        $updatedUser = $this->userService->updateUserPassword($user, $request->password, null);

        return $this->sendSuccessCollectionResponse(
            UserResource::make($updatedUser),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }

    /**
     * Update authenticated user profile information
     */
    public function updateProfile(UserProfileUpdateRequest $request)
    {
        $user = User::findOrFail(auth()->id())->load('roles');
        Gate::authorize('update', $user);
        $data = new UserDTO(
            $user->id,
            $request->first_name,
            $request->last_name,
            $request->name,
            $request->email,
            null,
            $request->phone,
            $request->designation,
            $request->address,
            null,
            null
        );
        $updatedUser = $this->userService->updateUserData($data, $user, true);
        return $this->sendSuccessCollectionResponse(
            UserResource::make($updatedUser),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }

    /**
     * Update authenticated user profile password
     */
    public function changeProfilePassword(UserPasswordUpdateRequest $request)
    {
        $user = User::findOrFail(auth()->id())->load('roles');
        Gate::authorize('update', User::class);
        $updatedUser = $this->userService->updateUserPassword(
            $user,
            $request->password,
            $request->current_password
        );
        return $this->sendSuccessCollectionResponse(
            UserResource::make($updatedUser),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }
}
