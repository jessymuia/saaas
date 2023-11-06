<?php

namespace App\Policies;

use App\Models\RefBillingType;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class RefBillingTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_BILLING_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any billing type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RefBillingType $refBillingType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_BILLING_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view billing type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_BILLING_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create billing type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RefBillingType $refBillingType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_BILLING_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update billing type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RefBillingType $refBillingType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_BILLING_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete billing type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RefBillingType $refBillingType): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_BILLING_TYPES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore billing type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RefBillingType $refBillingType): bool
    {
        //
        return false;
    }
}
