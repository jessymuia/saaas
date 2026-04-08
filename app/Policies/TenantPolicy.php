<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class TenantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_TENANTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any tenant');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tenant $tenant): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_TENANTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view tenant');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_TENANTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create tenant');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tenant $tenant): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_TENANTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update tenant');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tenant $tenant): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_TENANTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete tenant');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tenant $tenant): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_TENANTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore tenant');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tenant $tenant): bool
    {
        //
        return false;
    }
}
