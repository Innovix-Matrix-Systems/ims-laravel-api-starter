<?php

namespace App\Http\Controllers\Api\V1\Permission;

use App\Enums\ApiErrorCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\PermissionInsertRequest;
use App\Http\Resources\Permission\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Permission Management
 *
 * APIs for managing user permissions
 */
class PermissionController extends Controller
{
    /**
     * Get all permissions
     *
     * Retrieve a list of all permissions in the system.
     *
     * @apiResourceCollection App\Http\Resources\Permission\PermissionResource
     *
     * @apiResourceModel App\Models\Permission paginate=10
     */
    public function index()
    {
        // Gate::authorize('viewAny', Permission::class);
        $permissions = Permission::all();

        return PermissionResource::collection($permissions);
    }

    /**
     * Show the form for creating a new permission
     *
     * This method is not implemented.
     */
    public function create()
    {
        //
    }

    /**
     * Create a new permission
     *
     * Create a new permission with the provided name.
     *
     * @apiResource App\Http\Resources\Permission\PermissionResource
     *
     * @apiResourceModel App\Models\Permission
     */
    public function store(PermissionInsertRequest $request)
    {
        Gate::authorize('create', Permission::class);
        $permission = new Permission;
        $permission->name = $request->name;
        $permission->save();

        return PermissionResource::make($permission);
    }

    /**
     * Get permission details
     *
     * This method is not implemented.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing a permission
     *
     * This method is not implemented.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update a permission
     *
     * This method is not implemented.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Delete a permission
     *
     * Delete a permission from the system.
     *
     * @urlParam id string required The ID of the permission. Example: 1
     *
     * @response 204 No Content
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        Gate::authorize('delete', $permission);
        try {
            $isDeleted = $permission->delete();
            if (! $isDeleted) {
                return $this->fail(
                    ApiErrorCode::INTERNAL_SERVER_ERROR->value,
                    __('messages.permission.delete.fail'),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->fail(
                ApiErrorCode::INTERNAL_SERVER_ERROR->value,
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get user permissions
     *
     * Retrieve all permissions assigned to the authenticated user through their roles.
     *
     * @apiResourceCollection App\Http\Resources\Permission\PermissionResource
     *
     * @apiResourceModel App\Models\Permission
     */
    public function getUserPermissions(Request $request)
    {
        $user = $request->user();
        $permissions = $user->getPermissionsViaRoles();

        return PermissionResource::collection($permissions);
    }
}
