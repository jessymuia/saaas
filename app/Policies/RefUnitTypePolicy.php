<?php

namespace App\Policies;

use App\Models\RefUnitType;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class RefUnitTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_UNIT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any unit type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RefUnitType $refUnitType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_UNIT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view unit type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_UNIT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create unit type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RefUnitType $refUnitType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_UNIT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update unit type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RefUnitType $refUnitType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_UNIT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete unit type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RefUnitType $refUnitType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_UNIT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore unit type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RefUnitType $refUnitType): bool
    {
        //
        return false;
    }
}
