<?php

namespace App\Policies;

use App\Models\RefPropertyType;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class RefPropertyTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PROPERTY_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any property type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RefPropertyType $refPropertyType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PROPERTY_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view property type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_PROPERTY_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create property type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RefPropertyType $refPropertyType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_PROPERTY_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update property type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RefPropertyType $refPropertyType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_PROPERTY_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete property type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RefPropertyType $refPropertyType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_PROPERTY_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore property type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RefPropertyType $refPropertyType): bool
    {
        //
        return false;
    }
}
