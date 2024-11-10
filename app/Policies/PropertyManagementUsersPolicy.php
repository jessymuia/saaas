<?php

namespace App\Policies;

use App\Models\PropertyManagementUsers;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class PropertyManagementUsersPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PROPERTY_MANAGEMENT_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any property management users.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyManagementUsers $propertyManagementUsers): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PROPERTY_MANAGEMENT_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view property management users.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_PROPERTY_MANAGEMENT_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create property management users.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyManagementUsers $propertyManagementUsers): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_PROPERTY_MANAGEMENT_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update property management users.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyManagementUsers $propertyManagementUsers): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_PROPERTY_MANAGEMENT_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete property management users.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PropertyManagementUsers $propertyManagementUsers): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_PROPERTY_MANAGEMENT_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore property management users.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PropertyManagementUsers $propertyManagementUsers): bool
    {
        //
        return false;
    }
}
