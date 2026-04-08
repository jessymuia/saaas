<?php

namespace App\Policies;

use App\Models\MeterReading;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class MeterReadingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_METER_READINGS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view any meter reading');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MeterReading $meterReading): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::READ_METER_READINGS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to view meter reading');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::CREATE_METER_READINGS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to create meter reading');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MeterReading $meterReading): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::UPDATE_METER_READINGS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to update meter reading');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MeterReading $meterReading): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::DELETE_METER_READINGS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to delete meter reading');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MeterReading $meterReading): Response
    {
        //
        return $user->hasPermissionTo(AppPermissions::RESTORE_METER_READINGS_PERMISSION)
            ? Response::allow()
            : Response::deny('You do not have permissions to restore meter reading');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MeterReading $meterReading): bool
    {
        //
        return false;
    }
}
