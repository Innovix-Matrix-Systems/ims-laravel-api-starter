<?php

namespace App\Http\Controllers\Api\V1\Role;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleInsertUpdateRequest;
use App\Http\Requests\Role\RolePermissionAssignRequest;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use App\Services\Role\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Role Management
 *
 * APIs for managing roles in the system
 */
class RoleController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        protected RoleService $roleService,
    ) {}

    /**
     * Get all roles
     *
     * Retrieve a list of all roles in the system, excluding the super admin role.
     *
     * @apiResourceCollection App\Http\Resources\Role\RoleResource
     *
     * @apiResourceModel App\Models\Role paginate=10
     */
    public function index()
    {
        Gate::authorize('viewAny', Role::class);
        $roles = Role::where('id', '!=', UserRole::SUPER_ADMIN->id())->get();

        return RoleResource::collection($roles);
    }

    /**
     * Show the form for creating a new role
     *
     * This method is not implemented.
     */
    public function create(Request $request) {}

    /**
     * Create a new role
     *
     * Create a new role with the provided name and optional permissions.
     *
     * @apiResource App\Http\Resources\Role\RoleResource
     *
     * @apiResourceModel App\Models\Role
     */
    public function store(RoleInsertUpdateRequest $request)
    {
        Gate::authorize('create', Role::class);
        $role = $this->roleService->insertRole(
            $request->name,
            $request->permissions ?? []
        );

        return RoleResource::make($role);
    }

    /**
     * Get role details
     *
     * Retrieve detailed information about a specific role including its permissions.
     *
     * @urlParam id string required The ID of the role. Example: 1
     *
     * @apiResource App\Http\Resources\Role\RoleResource
     *
     * @apiResourceModel App\Models\Role paginate=10 with=permissions
     */
    public function show(string $id)
    {

        $role = Role::findOrFail($id)->load('permissions');
        Gate::authorize('view', $role);

        return RoleResource::make($role);
    }

    /**
     * Show the form for editing a role
     *
     * This method is not implemented.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update a role
     *
     * Update an existing role with a new name.
     *
     * @urlParam id string required The ID of the role. Example: 1
     *
     * @apiResource App\Http\Resources\Role\RoleResource
     *
     * @apiResourceModel App\Models\Role
     */
    public function update(RoleInsertUpdateRequest $request)
    {
        $role = Role::findOrFail((int) $request->id);
        Gate::authorize('update', $role);
        $name = $request->name;
        $updatedRole = $this->roleService->updateRole($role, $name);

        return RoleResource::make($updatedRole);
    }

    /**
     * Delete a role
     *
     * Delete a role from the system along with its permissions.
     *
     * @urlParam id string required The ID of the role. Example: 1
     *
     * @response 204 No Content
     */
    public function destroy(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        Gate::authorize('delete', $role);
        $this->roleService->deleteRole($role);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Assign permissions to a role
     *
     * Assign one or more permissions to a specific role.
     *
     * @apiResource App\Http\Resources\Role\RoleResource
     *
     * @apiResourceModel App\Models\Role paginate=10 with=permissions
     */
    public function assignPermission(RolePermissionAssignRequest $request)
    {
        Gate::authorize('create', Role::class);
        $role = Role::findOrFail((int) $request->id);
        $role->syncPermissions($request->permissions);

        return RoleResource::make($role->load('permissions'));
    }
}
