<?php

namespace App\Policies;

use App\Models\TenancyAgreement;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class TenancyAgreementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_TENANCY_AGREEMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any tenancy agreement');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TenancyAgreement $tenancyAgreement): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_TENANCY_AGREEMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view tenancy agreement');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_TENANCY_AGREEMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create tenancy agreement');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TenancyAgreement $tenancyAgreement): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_TENANCY_AGREEMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update tenancy agreement');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TenancyAgreement $tenancyAgreement): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_TENANCY_AGREEMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete tenancy agreement');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TenancyAgreement $tenancyAgreement): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_TENANCY_AGREEMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore tenancy agreement');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TenancyAgreement $tenancyAgreement): bool
    {
        //
        return false;
    }


    public function generateLeaseSchedule(User $user, TenancyAgreement $tenancyAgreement): Response
    {
        return Response::allow();
    }
}
