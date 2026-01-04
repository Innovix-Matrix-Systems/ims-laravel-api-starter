<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /** Determine whether the user can view any models. */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('user.view.all');
    }

    /** Determine whether the user can view the model. */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.view') || $user->id == $model->id;
    }

    /** Determine whether the user can create models. */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('user.create');
    }

    /** Determine whether the user can update the model. */
    public function update(User $user, User $model): bool
    {
        if ($model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->hasPermissionTo('user.update') || $user->id == $model->id;
    }

    /** Determine whether the user can assign a role to the model. */
    public function assignRole(User $user, User $model): bool
    {
        if ($model->isSuperAdmin()) {
            return false;
        }

        return $user->hasPermissionTo('user.role.assign');
    }

    /** Determine whether the user can delete the model. */
    public function delete(User $user, User $model): bool
    {
        if ($model->isSuperAdmin()) {
            return false;
        }

        return $user->hasPermissionTo('user.delete');
    }

    /** Determine whether the user can restore the model. */
    public function restore(User $user, User $model): bool
    {
        return $user->isSuperAdmin();
    }

    /** Determine whether the user can permanently delete the model. */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isSuperAdmin();
    }

    /** Determine whether the user can export models. */
    public function export(User $user): bool
    {
        return $user->hasPermissionTo('user.export');
    }

    /** Determine whether the user can import models. */
    public function import(User $user): bool
    {
        return $user->hasPermissionTo('user.import');
    }
}
