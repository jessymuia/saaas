<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class PropertyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasRole('admin') || (
            $user->hasPermissionTo(AppPermissions::READ_PROPERTIES_PERMISSION) &&
            $user->properties()->where('property_management_users.status', true)->exists()
        )
            ? Response::allow()
            : Response::deny('You do not have permissions to view property');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Property $property): Response
    {
        return $user->hasRole('admin') || (
            $user->hasPermissionTo(AppPermissions::READ_PROPERTIES_PERMISSION) &&
            $this->canAccessProperty($user, $property)
        )
            ? Response::allow()
            : Response::deny('You do not have permissions to view property');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        //
        return $user->hasRole('admin') || (
            $user->hasPermissionTo(AppPermissions::CREATE_PROPERTIES_PERMISSION) &&
            $user->properties()->where('property_management_users.status', true)->exists()
        )
            ? Response::allow()
            : Response::deny('You do not have permissions to create property');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Property $property): Response
    {
        //
        return $user->hasRole('admin') || (
            $user->hasPermissionTo(AppPermissions::UPDATE_PROPERTIES_PERMISSION) &&
            $this->canAccessProperty($user, $property)
        )
            ? Response::allow()
            : Response::deny('You do not have permissions to update property');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Property $property): Response
    {
        //
        return $user->hasRole('admin') || (
            $user->hasPermissionTo(AppPermissions::DELETE_PROPERTIES_PERMISSION) &&
            $this->canAccessProperty($user, $property)
        )
            ? Response::allow()
            : Response::deny('You do not have permissions to delete property');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Property $property): Response
    {
        //
        return $user->hasRole('admin') || (
            $user->hasPermissionTo(AppPermissions::RESTORE_PROPERTIES_PERMISSION) &&
            $this->canAccessProperty($user, $property)
        )
            ? Response::allow()
            : Response::deny('You do not have permissions to restore property');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Property $property): bool
    {
        //
        return false;
    }

    protected function canAccessProperty(User $user, Property $property): bool
    {
        return $user->properties()
            ->where('properties.id', $property->id)
            ->where('property_management_users.status', true)
            ->exists();
    }
}
