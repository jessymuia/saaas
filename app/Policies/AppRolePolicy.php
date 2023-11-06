<?php

namespace App\Policies;

use App\Models\AppRole;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class AppRolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_ROLES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any app role');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AppRole $appRole): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_ROLES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view app role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_ROLES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create app role');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AppRole $appRole): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_ROLES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update app role');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AppRole $appRole): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_ROLES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete app role');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AppRole $appRole): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_ROLES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore app role');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AppRole $appRole): bool
    {
        //
        return false;
    }
}
