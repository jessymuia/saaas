<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SystemAdmin;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserPolicy
{
    public function viewAny(Authenticatable $user): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }
        return $user->hasPermissionTo(AppPermissions::READ_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any user');
    }

    public function view(Authenticatable $user, User $model): Response
    {
        if ($user instanceof SystemAdmin) return Response::allow();
        return $user->hasPermissionTo(AppPermissions::READ_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view user');
    }

    public function create(Authenticatable $user): Response
    {
        if ($user instanceof SystemAdmin) return Response::allow();
        return $user->hasPermissionTo(AppPermissions::CREATE_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create user');
    }

    public function update(Authenticatable $user, User $model): Response
    {
        if ($user instanceof SystemAdmin) return Response::allow();
        return $user->hasPermissionTo(AppPermissions::UPDATE_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update user');
    }

    public function delete(Authenticatable $user, User $model): Response
    {
        if ($user instanceof SystemAdmin) return Response::allow();
        return $user->hasPermissionTo(AppPermissions::DELETE_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete user');
    }

    public function restore(Authenticatable $user, User $model): Response
    {
        if ($user instanceof SystemAdmin) return Response::allow();
        return $user->hasPermissionTo(AppPermissions::RESTORE_USERS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore user');
    }

    public function forceDelete(Authenticatable $user, User $model): bool
    {
        return false;
    }
}