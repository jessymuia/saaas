<?php

namespace App\Policies;

use App\Models\SentEmails;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class SentEmailsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_SENT_EMAILS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any sent email');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SentEmails $sentEmails): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_SENT_EMAILS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view sent email');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_SENT_EMAILS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create sent email');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SentEmails $sentEmails): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_SENT_EMAILS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update sent email');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SentEmails $sentEmails): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_SENT_EMAILS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete sent email');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SentEmails $sentEmails): bool
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_SENT_EMAILS_PERMISSION);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SentEmails $sentEmails): bool
    {
        //
        return false;
    }
}
