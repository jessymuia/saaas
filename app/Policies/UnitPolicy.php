<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class UnitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_UNITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any unit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Unit $unit): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_UNITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view unit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_UNITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create unit');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Unit $unit): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_UNITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update unit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Unit $unit): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_UNITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete unit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Unit $unit): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_UNITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore unit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Unit $unit): bool
    {
        //
        return false;
    }
}
