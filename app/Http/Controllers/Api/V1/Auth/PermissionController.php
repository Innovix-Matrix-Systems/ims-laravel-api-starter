<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PermissionInsertRequest;
use App\Http\Resources\Auth\PermissionResource;
use App\Http\Services\Auth\RolePermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
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
        Gate::authorize('viewAny', Permission::class);
        $permissions = Permission::all();
        return $this->sendSuccessCollectionResponse(
            PermissionResource::collection($permissions),
            __('http-statuses.200'),
            Response::HTTP_OK
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionInsertRequest $request)
    {
        Gate::authorize('create', Permission::class);
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->save();
        return $this->sendSuccessResponse(
            PermissionResource::make($permission),
            __('http-statuses.201'),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        Gate::authorize('delete', $permission);
        $this->rolePermissionService->deletePermission($permission);
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
