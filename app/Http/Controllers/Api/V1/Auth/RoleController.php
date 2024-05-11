<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RoleInsertUpdateRequest;
use App\Http\Requests\Auth\RolePermissionAssignRequest;
use App\Http\Resources\Auth\RoleResource;
use App\Http\Services\Auth\RolePermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        protected RolePermissionService $rolePermissionService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Role::class);
        $roles = Role::all();
        return $this->sendSuccessCollectionResponse(
            RoleResource::collection($roles),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleInsertUpdateRequest $request)
    {
        Gate::authorize('create', Role::class);
        $role = $this->rolePermissionService->insertRole(
            $request->name,
            $request->permissions ?? []
        );
        return $this->sendSuccessResponse(
            RoleResource::make($role),
            __('http-statuses.201'),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $role = Role::findOrFail($id)->load('permissions');
        Gate::authorize('view', $role);
        return $this->sendSuccessResponse(
            RoleResource::make($role),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleInsertUpdateRequest $request)
    {
        $role = Role::findOrFail($request->id);
        Gate::authorize('update', $role);
        $name = $request->name;
        $updatedRole = $this->rolePermissionService->updateRole($role, $name);
        return $this->sendSuccessResponse(
            RoleResource::make($updatedRole),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        Gate::authorize('delete', $role);
        $this->rolePermissionService->deleteRole($role);
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    public function assignPermission(RolePermissionAssignRequest $request)
    {
        Gate::authorize('create', Role::class);
        $role = Role::findOrFail($request->id);
        $role->syncPermissions($request->permissions);
        return $this->sendSuccessResponse(
            RoleResource::make($role->load('permissions')),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }
}
