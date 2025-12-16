<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\Role;
use App\Registry\PermissionRegistry;
use Illuminate\Console\Command;

class SyncPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions from PermissionRegistry into the database';

    /** Execute the console command. */
    public function handle()
    {
        try {
            // Get permissions from trait
            $traitPermissions = PermissionRegistry::getAllModulePermissions();

            // Get existing permissions from database
            $existingPermissionNames = Permission::pluck('name')->toArray();

            // Extract permission names from trait for comparison
            $traitPermissionNames = array_column($traitPermissions, 'name');

            // Find missing permissions
            $missingPermissionNames = array_diff($traitPermissionNames, $existingPermissionNames);

            if (empty($missingPermissionNames)) {
                $this->info('No new permissions to sync.');
            } else {
                // Prepare data for batch insert
                // Filter trait permissions to only include missing ones
                $permissionsToInsert = array_filter($traitPermissions, function ($permission) use ($missingPermissionNames) {
                    return in_array($permission['name'], $missingPermissionNames);
                });

                // Unique permissions by name to prevent duplicates
                $uniquePermissions = [];
                foreach ($permissionsToInsert as $permission) {
                    $uniquePermissions[$permission['name']] = $permission;
                }
                $permissionsToInsert = array_values($uniquePermissions);

                // Batch insert missing permissions
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                Permission::insert($permissionsToInsert);

                $this->info(count($permissionsToInsert) . ' new permissions synced successfully.');
            }

            // Reassign all permissions to superadmin role after sync
            $this->reassignSuperAdminPermissions();

            return 0;
        } catch (\Exception $e) {
            $this->error('Error syncing permissions: ' . $e->getMessage());

            return 1;
        }
    }

    /** Reassign all permissions to the superadmin role. */
    private function reassignSuperAdminPermissions()
    {
        // Get the superadmin role
        $superAdminRole = Role::where('name', UserRole::SUPER_ADMIN->value)
            ->where('guard_name', config('auth.defaults.guard'))
            ->first();

        if ($superAdminRole) {
            // Refresh the permission list and assign all to superadmin
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $superAdminRole->syncPermissions(Permission::all());
            $this->info('All permissions reassigned to Super-Admin role successfully.');
        } else {
            $this->error('Super-Admin role not found. Please run role seeder first.');
        }
    }
}
