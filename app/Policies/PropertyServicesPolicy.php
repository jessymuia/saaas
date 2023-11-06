<?php

namespace App\Policies;

use App\Models\PropertyServices;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class PropertyServicesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PROPERTY_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any property service');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PropertyServices $propertyServices): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PROPERTY_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view property service');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_PROPERTY_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create property service');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PropertyServices $propertyServices): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_PROPERTY_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update property service');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PropertyServices $propertyServices): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_PROPERTY_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete property service');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PropertyServices $propertyServices): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_PROPERTY_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore property service');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PropertyServices $propertyServices): bool
    {
        //
        return false;
    }
}
