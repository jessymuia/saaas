<?php

namespace App\Policies;

use App\Models\RefUtility;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class RefUtilityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any utility');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RefUtility $refUtility): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view utility');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create utility');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RefUtility $refUtility): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update utility');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RefUtility $refUtility): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete utility');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RefUtility $refUtility): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore utility');;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RefUtility $refUtility): bool
    {
        //
        return false;
    }
}
