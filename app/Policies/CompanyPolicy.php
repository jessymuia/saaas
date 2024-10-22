<?php

namespace App\Policies;

use App\Models\CompanyDetails;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_COMPANIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any company');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CompanyDetails $company): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_COMPANIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view company');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_COMPANIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create company');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CompanyDetails $company): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_COMPANIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update company');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CompanyDetails $company): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_COMPANIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete company');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CompanyDetails $company): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_COMPANIES_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore company');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CompanyDetails $company): bool
    {
        //
        return false;
    }
}
