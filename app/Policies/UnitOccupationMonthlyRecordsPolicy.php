<?php

namespace App\Policies;

use App\Models\UnitOccupationMonthlyRecords;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class UnitOccupationMonthlyRecordsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any unit occupation monthly records');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UnitOccupationMonthlyRecords $unitOccupationMonthlyRecords): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view unit occupation monthly records');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create unit occupation monthly records');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UnitOccupationMonthlyRecords $unitOccupationMonthlyRecords): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update unit occupation monthly records');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UnitOccupationMonthlyRecords $unitOccupationMonthlyRecords): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete unit occupation monthly records');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UnitOccupationMonthlyRecords $unitOccupationMonthlyRecords): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore unit occupation monthly records');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UnitOccupationMonthlyRecords $unitOccupationMonthlyRecords): bool
    {
        //
        return false;
    }
}
