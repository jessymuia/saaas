<?php

namespace App\Policies;

use App\Models\TenancyBill;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class TenancyBillPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_TENANCY_BILLS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any tenancy bill');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TenancyBill $tenancyBill): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_TENANCY_BILLS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view tenancy bill');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_TENANCY_BILLS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create tenancy bill');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TenancyBill $tenancyBill): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_TENANCY_BILLS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update tenancy bill');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TenancyBill $tenancyBill): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_TENANCY_BILLS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete tenancy bill');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TenancyBill $tenancyBill): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_TENANCY_BILLS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore tenancy bill');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TenancyBill $tenancyBill): bool
    {
        //
        return false;
    }
}
