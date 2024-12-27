<?php

namespace App\Policies;

use OwenIt\Auditing\Models\Audit;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class AuditPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_AUDITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any audits');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Audit $audit): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_AUDITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view this audit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_AUDITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create audits');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Audit $audit): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_AUDITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update audits');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Audit $audit): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_AUDITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete audits');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Audit $audit): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_AUDITS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore audits');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Audit $audit): bool
    {
        //
        return false;
    }
}
