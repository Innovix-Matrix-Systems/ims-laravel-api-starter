<?php

namespace App\Http\Controllers\Api\V1\User;

use App\DTOs\User\UserDTO;
use App\DTOs\User\UserFilterDTO;
use App\Enums\UserRole;
use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AdminAssignUserRoleRequest;
use App\Http\Requests\User\AdminUserPasswordUpdateRequest;
use App\Http\Requests\User\UserInsertUpdateRequest;
use App\Http\Requests\User\UserPasswordUpdateRequest;
use App\Http\Requests\User\UserProfileAvatarUpdateRequest;
use App\Http\Requests\User\UserProfileUpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group User Management
 *
 * APIs for managing users, user profiles, and user-related operations
 */
class UserController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        protected UserService $userService,
    ) {}

    /**
     * Get all users
     *
     * Retrieve a paginated list of users with optional filtering by role, status, and search.
     *
     * @queryParam search string Search term for name, email, or phone. Example: john
     * @queryParam is_active boolean Filter users by active status. Example: true
     * @queryParam role_name string Filter by exact role name. Example: Admin
     * @queryParam order_by string Field to order results by. Example: created_at
     * @queryParam order_direction string Direction to order results ('asc' or 'desc'). Example: desc
     * @queryParam per_page int Number of results per page (default: 10). Example: 15
     * @queryParam page int Page number for pagination. Example: 1
     *
     * @apiResourceCollection App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User paginate=10 with=roles
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $filters = UserFilterDTO::fromRequest($request);
        $users = $this->userService->getAllUsers($filters);

        return UserResource::collection($users);
    }

    /**
     * Create new user
     *
     * Create a new user account with profile information and assign roles.
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User
     */
    public function store(UserInsertUpdateRequest $request)
    {
        Gate::authorize('create', User::class);

        // Filter out SUPER_ADMIN role to prevent unauthorized elevation
        $roles = collect($request->validated()['roles'] ?? [])
            ->map(fn ($roleId) => (int) $roleId)
            ->reject(fn ($id) => $id === UserRole::SUPER_ADMIN->id())
            ->values()
            ->all();

        $data = UserDTO::fromRequest($request, null, $roles);
        $user = $this->userService->insertUserData($data);

        return UserResource::make($user);
    }

    /**
     * Get user details
     *
     * Retrieve detailed information about a specific user including roles and branch.
     *
     * @urlParam id string required The ID of the user. Example: 1
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User with=roles,media
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id)->load('roles', 'media');
        Gate::authorize('view', $user);

        return UserResource::make($user);

    }

    /**
     * Update user
     *
     * Update user profile information and settings.
     *
     * @urlParam id string required The ID of the user. Example: 1
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User
     */
    public function update(UserInsertUpdateRequest $request)
    {
        $user = User::query()->findOrFail((int) $request->id);
        Gate::authorize('update', $user);

        $data = UserDTO::fromRequest($request, $user);

        $updatedUser = $this->userService->updateUserData($data, $user);

        return UserResource::make($updatedUser);
    }

    /**
     * Delete user
     *
     * Delete a user account (cannot delete super admin users).
     *
     * @urlParam id string required The ID of the user. Example: 1
     *
     * @response 204 No Content
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail((int) $id);
        Gate::authorize('delete', $user);

        $isDeleted = $this->userService->deleteUser($user);
        if (! $isDeleted) {
            return $this->fail(
                __('USER_DELETE_FAILED'),
                __('messages.user.delete.failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->successNoContent();
    }

    /**
     * Assign roles to user
     *
     * Assign one or more roles to a specific user.
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User with=roles
     */
    public function assignRole(AdminAssignUserRoleRequest $request)
    {
        $user = User::find((int) $request->id);
        Gate::authorize('assignRole', $user);
        $roles = collect($request->validated()['roles'] ?? [])
            ->map(fn ($roleId) => (int) $roleId)
            ->reject(fn ($id) => $id === UserRole::SUPER_ADMIN->id())
            ->values()
            ->all();
        $updatedUser = $this->userService->assignUserRole($user, $roles);

        return UserResource::make($updatedUser->load('roles'));
    }

    /**
     * Change user password
     *
     * Update the password for a specific user (admin function).
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User
     */
    public function changePassword(AdminUserPasswordUpdateRequest $request)
    {
        $user = User::query()->findOrFail((int) $request->user_id);
        Gate::authorize('update', $user);
        $updatedUser = $this->userService->updateUserPassword($user, $request->password, null);

        return UserResource::make($updatedUser);
    }

    /**
     * Get current user profile
     *
     * Retrieve the authenticated user's profile information.
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User with=roles,media
     */
    public function getProfileData(Request $request)
    {
        $user = User::findOrFail(auth()->id())->load('roles', 'media');
        Gate::authorize('view', $user);

        return UserResource::make($user);
    }

    /**
     * Update current user profile
     *
     * Update the authenticated user's profile information.
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User
     */
    public function updateProfile(UserProfileUpdateRequest $request)
    {
        $user = User::findOrFail(auth()->id());
        Gate::authorize('update', $user);
        $data = UserDTO::fromRequest($request, $user);
        $updatedUser = $this->userService->updateUserData($data, $user, true);

        return UserResource::make($updatedUser);
    }

    /**
     * Change current user password
     *
     * Update the authenticated user's password.
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User
     */
    public function changeProfilePassword(UserPasswordUpdateRequest $request)
    {
        $user = User::findOrFail(auth()->id());
        Gate::authorize('update', $user);
        $updatedUser = $this->userService->updateUserPassword(
            $user,
            $request->password,
            $request->current_password
        );

        return UserResource::make($updatedUser);
    }

    /**
     * Update user avatar
     *
     * Upload and update the user's profile avatar image.
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User with=media
     */
    public function updateProfileAvatar(UserProfileAvatarUpdateRequest $request)
    {
        $user = User::findOrFail(auth()->id());
        Gate::authorize('update', $user);
        $updatedUser = $this->userService->updateUserAvatar($user, $request->avatar);

        return UserResource::make($updatedUser->load('media'));
    }

    /**
     * Export Users
     *
     * Export user records with filtering options
     *
     * @queryParam search string optional Search term for name, email, or phone. Example: john
     * @queryParam is_active boolean optional Filter users by active status. Example: true
     * @queryParam role_name string optional Filter by exact role name. Example: Admin
     * @queryParam order_by string optional Field to order results by. Example: name. Default: created_at
     * @queryParam order_direction string optional Direction to order results ('asc' or 'desc'). Example: desc. Default: asc
     *
     * @response 200 file Binary Excel file (.xlsx)
     */
    public function exportUserData(Request $request)
    {
        Gate::authorize('export', User::class);

        $filters = UserFilterDTO::fromRequest($request);

        return Excel::download(
            new UserExport($filters),
            'user_data_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }
}
