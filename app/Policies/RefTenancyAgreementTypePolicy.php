<?php

namespace App\Policies;

use App\Filament\Resources\RentPaymentResource\Pages\EditRentPayment;
use App\Models\RefTenancyAgreementType;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\App;

class RefTenancyAgreementTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_TENANCY_AGREEMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any tenancy agreement type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RefTenancyAgreementType $refTenancyAgreementType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_TENANCY_AGREEMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view tenancy agreement type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_TENANCY_AGREEMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create tenancy agreement type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RefTenancyAgreementType $refTenancyAgreementType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_TENANCY_AGREEMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update tenancy agreement type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RefTenancyAgreementType $refTenancyAgreementType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_TENANCY_AGREEMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete tenancy agreement type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RefTenancyAgreementType $refTenancyAgreementType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_TENANCY_AGREEMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore tenancy agreement type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RefTenancyAgreementType $refTenancyAgreementType): bool
    {
        //
        return false;
    }
}
