<?php

namespace App\Policies;

use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\App;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any user');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view user');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create user');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update user');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete user');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore user');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
        return false;
    }
}
