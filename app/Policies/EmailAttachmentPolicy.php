<?php

namespace App\Policies;

use App\Models\EmailAttachments;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class EmailAttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        // check if user has permission to view email attachments
        return $user->hasPermissionTo(AppPermissions::READ_EMAIL_ATTACHMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any email attachments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmailAttachments $emailAttachments): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_EMAIL_ATTACHMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view email attachments');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_EMAIL_ATTACHMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create email attachments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmailAttachments $emailAttachments): Response
    {
        // check if user has permission to update email attachments
        return $user->hasPermissionTo(AppPermissions::UPDATE_EMAIL_ATTACHMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update email attachments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmailAttachments $emailAttachments): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_EMAIL_ATTACHMENTS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete email attachments');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EmailAttachments $emailAttachments): bool
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_EMAIL_ATTACHMENTS_PERMISSION);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EmailAttachments $emailAttachments): bool
    {
        //
        return false;
    }
}
