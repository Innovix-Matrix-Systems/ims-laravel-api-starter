<?php

namespace App\Http\Services\Auth;

use App\Exceptions\BasicValidationErrorException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService
{

    const UNALTERABLE_ROLE_IDS = [1, 2, 3];
    const UNALTERABLE_PERMISSION_IDS = [1,2,3,4,5,6,7,8,9,10];

    private function isUnalterableRole(int $id): bool
    {
        return in_array($id, self::UNALTERABLE_ROLE_IDS);
    }

    private function checkAndSendUnalterableRoleError(int $id): void
    {
        if ($this->isUnalterableRole($id)) {
            throw new BasicValidationErrorException(
                Response::HTTP_FORBIDDEN,
                0,
                __('http-statuses.403')
            );
        }
    }

    public function insertRole(string $name, array $permissions = []): Role
    {
        $role = new Role();
        $role->name = $name;

        DB::beginTransaction();
        try {
            $role->save();

            if (!empty($permissions)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                $role->givePermissionTo($permissions);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }


        return $role->load('permissions');
    }

    public function updateRole(Role $role, string $name): Role
    {
        $this->checkAndSendUnalterableRoleError($role->id);

        $role->name = $name;
        $role->save();

        return $role;
    }

    public function deleteRole(Role $role): void
    {
        $this->checkAndSendUnalterableRoleError($role->id);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::beginTransaction();
        try {
            //unassign all permission from this role
            if($role->permissions->count() > 0) {
                $role->syncPermissions([]);
            }
            //delete role
            $role->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function deletePermission(Permission $permission): void
    {
        if(in_array($permission->id, self::UNALTERABLE_PERMISSION_IDS)) {
            throw new BasicValidationErrorException(
                Response::HTTP_FORBIDDEN,
                0,
                __('http-statuses.403')
            );
        }
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permission->delete();
    }
}
