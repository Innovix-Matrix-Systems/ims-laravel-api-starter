<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Exceptions\ApiException;
use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Services\Role\RoleService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Spatie\Permission\PermissionRegistrar;
use Tests\Mock\RoleMockData;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->roleRepository = Mockery::mock(RoleRepositoryInterface::class);
    $this->roleService = new RoleService($this->roleRepository);
});

test('insertRole creates role via repository', function () {
    $roleName = RoleMockData::getRoleData()['name'];
    $permissions = ['view_users'];

    $role = Mockery::mock(Role::class);
    $role->shouldReceive('load')->with('permissions')->andReturnSelf();
    $role->shouldReceive('givePermissionTo')->with($permissions)->once();
    DB::shouldReceive('beginTransaction')->once();
    DB::shouldReceive('commit')->once();

    // Mock PermissionRegistrar to prevent permission caching issues
    /** @var \Mockery\MockInterface|\Spatie\Permission\PermissionRegistrar $registrar */
    $registrar = Mockery::mock(PermissionRegistrar::class);
    $registrar->shouldReceive('forgetCachedPermissions')->once();
    app()->instance(PermissionRegistrar::class, $registrar);

    $this->roleRepository->shouldReceive('create')
        ->once()
        ->with(['name' => $roleName])
        ->andReturn($role);

    $result = $this->roleService->insertRole($roleName, $permissions);

    expect($result)->toBe($role);
});

test('updateRole updates role via repository', function () {
    $roleName = RoleMockData::getUpdateRoleData()['name'];
    $role = Mockery::mock(Role::class);
    $role->shouldReceive('getAttribute')->with('id')->andReturn(999);

    $this->roleRepository->shouldReceive('update')
        ->once()
        ->with($role, ['name' => $roleName])
        ->andReturn($role);

    $result = $this->roleService->updateRole($role, $roleName);

    expect($result)->toBe($role);
});

test('updateRole throws exception for unalterable role', function () {
    $role = Mockery::mock(Role::class);
    $role->shouldReceive('getAttribute')->with('id')->andReturn(UserRole::SUPER_ADMIN->id());

    $this->expectException(ApiException::class);
    $this->expectExceptionMessage(__('messages.role.delete.unalterable.fail'));

    $this->roleService->updateRole($role, 'New Name');
});

test('deleteRole deletes role via repository', function () {
    $role = Mockery::mock(Role::class);
    $role->shouldReceive('getAttribute')->with('id')->andReturn(999);
    $role->shouldReceive('getAttribute')->with('permissions')->andReturn(collect(['p1']));

    $role->shouldReceive('syncPermissions')->with([])->once();

    DB::shouldReceive('beginTransaction')->once();
    DB::shouldReceive('commit')->once();

    $registrar = Mockery::mock(PermissionRegistrar::class);
    $registrar->shouldReceive('forgetCachedPermissions')->once();
    app()->instance(PermissionRegistrar::class, $registrar);

    $this->roleRepository->shouldReceive('delete')
        ->once()
        ->with($role);

    $this->roleService->deleteRole($role);
});

test('deleteRole throws exception for unalterable role', function () {
    $role = Mockery::mock(Role::class);
    $role->shouldReceive('getAttribute')->with('id')->andReturn(UserRole::SUPER_ADMIN->id());

    $this->expectException(ApiException::class);
    $this->expectExceptionMessage(__('messages.role.delete.unalterable.fail'));

    $this->roleService->deleteRole($role);
});
