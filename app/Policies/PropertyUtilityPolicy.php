<?php

namespace App\Policies;

use App\Models\PropertyUtility;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class PropertyUtilityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PROPERTY_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any property utility');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyUtility $propertyUtility): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PROPERTY_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view property utility');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_PROPERTY_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create property utility');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyUtility $propertyUtility): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_PROPERTY_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update property utility');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyUtility $propertyUtility): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_PROPERTY_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete property utility');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PropertyUtility $propertyUtility): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_PROPERTY_UTILITIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore property utility');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PropertyUtility $propertyUtility): bool
    {
        //
        return false;
    }
}
