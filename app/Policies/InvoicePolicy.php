<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_INVOICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any invoice');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_INVOICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view invoice');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_INVOICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create invoice');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_INVOICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update invoice');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_INVOICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete invoice');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invoice $invoice): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_INVOICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore invoice');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        //
        return false;
    }
}
