<?php

namespace App\Policies;

use App\Models\RefPaymentType;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class RefPaymentTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PAYMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any payment type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RefPaymentType $refPaymentType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_PAYMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view payment type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_PAYMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create payment type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RefPaymentType $refPaymentType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_PAYMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update payment type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RefPaymentType $refPaymentType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_PAYMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete payment type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RefPaymentType $refPaymentType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_PAYMENT_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore payment type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RefPaymentType $refPaymentType): bool
    {
        //
        return false;
    }
}
