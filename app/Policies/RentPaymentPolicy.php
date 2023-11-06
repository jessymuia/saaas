<?php

namespace App\Policies;

use App\Models\RentPayment;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class RentPaymentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_RENT_PAYMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any rent payment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RentPayment $rentPayment): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_RENT_PAYMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view rent payment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_RENT_PAYMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create rent payment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RentPayment $rentPayment): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_RENT_PAYMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update rent payment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RentPayment $rentPayment): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_RENT_PAYMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete rent payment');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RentPayment $rentPayment): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_RENT_PAYMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore rent payment');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RentPayment $rentPayment): bool
    {
        //
        return false;
    }
}
