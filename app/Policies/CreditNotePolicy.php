<?php

namespace App\Policies;

use App\Models\CreditNote;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class CreditNotePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_CREDIT_NOTES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any credit note');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CreditNote $creditNote): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_CREDIT_NOTES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view credit note');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_CREDIT_NOTES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create credit note');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CreditNote $creditNote): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_CREDIT_NOTES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update credit note');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CreditNote $creditNote): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_CREDIT_NOTES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete credit note');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CreditNote $creditNote): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_CREDIT_NOTES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore credit note');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CreditNote $creditNote): bool
    {
        //
        return false;
    }
}
