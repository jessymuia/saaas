<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\SystemAdmin;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    /**
     * Super Admin (central) gets full access to everything.
     * Tenant users use permission-based access.
     */

    public function viewAny(User|SystemAdmin $user): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(AppPermissions::READ_INVOICES_PERMISSION ?? 'read-invoices')
            ? Response::allow()
            : Response::deny('You do not have permissions to view invoices.');
    }

    public function view(User|SystemAdmin $user, Invoice $invoice): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(AppPermissions::READ_INVOICES_PERMISSION ?? 'read-invoices')
            ? Response::allow()
            : Response::deny('You do not have permission to view this invoice.');
    }

    public function create(User|SystemAdmin $user): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(AppPermissions::CREATE_INVOICES_PERMISSION ?? 'create-invoices')
            ? Response::allow()
            : Response::deny('You do not have permission to create invoices.');
    }

    public function update(User|SystemAdmin $user, Invoice $invoice): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(AppPermissions::UPDATE_INVOICES_PERMISSION ?? 'update-invoices')
            ? Response::allow()
            : Response::deny('You do not have permission to update this invoice.');
    }

    public function delete(User|SystemAdmin $user, Invoice $invoice): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(AppPermissions::DELETE_INVOICES_PERMISSION ?? 'delete-invoices')
            ? Response::allow()
            : Response::deny('You do not have permission to delete this invoice.');
    }
}