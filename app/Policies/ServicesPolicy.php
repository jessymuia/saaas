<?php

namespace App\Policies;

use App\Models\Services;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class ServicesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any services');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Services $services): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view services');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create services');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Services $services): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update services');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Services $services): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete services');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Services $services): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_SERVICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore services');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Services $services): bool
    {
        //
        return false;
    }
}
