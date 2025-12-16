<?php

namespace App\Services\Role;

use App\Enums\UserRole;
use App\Exceptions\ApiException;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RoleService
{
    /** Error code for unalterable role operations. */
    const UNALTERABLE_ROLE_DELETE_ERROR_CODE = 'UNALTERABLE_ROLE_DELETE_ERROR';

    public function insertRole(string $name, array $permissions = []): Role
    {
        $role = new Role;
        $role->name = $name;

        DB::beginTransaction();
        try {
            $role->save();

            if (! empty($permissions)) {
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
            // unassign all permission from this role
            if ($role->permissions->count() > 0) {
                $role->syncPermissions([]);
            }
            // delete role
            $role->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /** List of role IDs that are not allowed to be deleted or updated. */
    private static function unalterableRoleIds(): array
    {
        return [
            UserRole::SUPER_ADMIN->id(),
            UserRole::ADMIN->id(),
            UserRole::USER->id(),
        ];
    }

    private function isUnalterableRole(int $id): bool
    {
        return in_array($id, self::unalterableRoleIds(), true);
    }

    private function checkAndSendUnalterableRoleError(int $id): void
    {
        if ($this->isUnalterableRole($id)) {
            throw new ApiException(
                Response::HTTP_FORBIDDEN,
                self::UNALTERABLE_ROLE_DELETE_ERROR_CODE,
                __('messages.role.delete.unalterable.fail')
            );
        }
    }
}
