<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VacationNotices;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class VacationNoticesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_VACATION_NOTICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any vacation notices');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VacationNotices $vacationNotices): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_VACATION_NOTICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view vacation notices');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_VACATION_NOTICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create vacation notices');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VacationNotices $vacationNotices): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_VACATION_NOTICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update vacation notices');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VacationNotices $vacationNotices): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_VACATION_NOTICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete vacation notices');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VacationNotices $vacationNotices): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_VACATION_NOTICES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore vacation notices');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VacationNotices $vacationNotices): bool
    {
        //
        return false;
    }
}
