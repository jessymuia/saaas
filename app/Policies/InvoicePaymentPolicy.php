<?php

namespace App\Policies;

use App\Models\InvoicePayment;
use App\Models\SystemAdmin;
use App\Models\User;
use App\Utils\AppPermissions;
use Illuminate\Auth\Access\Response;

class InvoicePaymentPolicy
{
    /**
     * Super Admin (central) gets full access.
     * Tenant users use permission-based checks.
     */

    public function viewAny(User|SystemAdmin $user): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(
            AppPermissions::READ_INVOICE_PAYMENTS_PERMISSION ?? 'read-invoice-payments'
        )
            ? Response::allow()
            : Response::deny('You do not have permissions to view any payment record.');
    }

    public function view(User|SystemAdmin $user, InvoicePayment $invoicePayment): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(
            AppPermissions::READ_INVOICE_PAYMENTS_PERMISSION ?? 'read-invoice-payments'
        )
            ? Response::allow()
            : Response::deny('You do not have permission to view this payment record.');
    }

    /**
     * You can add more methods if they exist (create, update, delete, etc.)
     */
    public function create(User|SystemAdmin $user): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(
            AppPermissions::CREATE_INVOICE_PAYMENTS_PERMISSION ?? 'create-invoice-payments'
        )
            ? Response::allow()
            : Response::deny('You do not have permission to create payment records.');
    }

    public function update(User|SystemAdmin $user, InvoicePayment $invoicePayment): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(
            AppPermissions::UPDATE_INVOICE_PAYMENTS_PERMISSION ?? 'update-invoice-payments'
        )
            ? Response::allow()
            : Response::deny('You do not have permission to update this payment record.');
    }

    public function delete(User|SystemAdmin $user, InvoicePayment $invoicePayment): Response
    {
        if ($user instanceof SystemAdmin) {
            return Response::allow();
        }

        return $user->hasPermissionTo(
            AppPermissions::DELETE_INVOICE_PAYMENTS_PERMISSION ?? 'delete-invoice-payments'
        )
            ? Response::allow()
            : Response::deny('You do not have permission to delete this payment record.');
    }
}