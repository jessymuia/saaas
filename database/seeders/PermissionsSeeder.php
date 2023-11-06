<?php

namespace Database\Seeders;

use App\Utils\AppPermissions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // seed the permissions table with all the permissions

        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        // roles permissions
        Permission::findOrCreate(AppPermissions::CREATE_ROLES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_ROLES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_ROLES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_ROLES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_ROLES_PERMISSION);

        // credit notes
        Permission::findOrCreate(AppPermissions::CREATE_CREDIT_NOTES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_CREDIT_NOTES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_CREDIT_NOTES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_CREDIT_NOTES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_CREDIT_NOTES_PERMISSION);

        // invoices
        Permission::findOrCreate(AppPermissions::CREATE_INVOICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_INVOICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_INVOICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_INVOICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_INVOICES_PERMISSION);

        // properties
        Permission::findOrCreate(AppPermissions::CREATE_PROPERTIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_PROPERTIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_PROPERTIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_PROPERTIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_PROPERTIES_PERMISSION);

        // units
        Permission::findOrCreate(AppPermissions::CREATE_UNITS_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_UNITS_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_UNITS_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_UNITS_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_UNITS_PERMISSION);

        // rent payments
        Permission::findOrCreate(AppPermissions::CREATE_RENT_PAYMENTS_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_RENT_PAYMENTS_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_RENT_PAYMENTS_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_RENT_PAYMENTS_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_RENT_PAYMENTS_PERMISSION);

        // services
        Permission::findOrCreate(AppPermissions::CREATE_SERVICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_SERVICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_SERVICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_SERVICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_SERVICES_PERMISSION);

        // tenants
        Permission::findOrCreate(AppPermissions::CREATE_TENANTS_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_TENANTS_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_TENANTS_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_TENANTS_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_TENANTS_PERMISSION);

        // users
        Permission::findOrCreate(AppPermissions::CREATE_USERS_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_USERS_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_USERS_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_USERS_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_USERS_PERMISSION);

        // roles
        Permission::findOrCreate(AppPermissions::CREATE_ROLES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_ROLES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_ROLES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_ROLES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_ROLES_PERMISSION);

        // utilities
        Permission::findOrCreate(AppPermissions::CREATE_UTILITIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_UTILITIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_UTILITIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_UTILITIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_UTILITIES_PERMISSION);

        // vacation notices
        Permission::findOrCreate(AppPermissions::CREATE_VACATION_NOTICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_VACATION_NOTICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_VACATION_NOTICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_VACATION_NOTICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_VACATION_NOTICES_PERMISSION);

        // billing types
        Permission::findOrCreate(AppPermissions::CREATE_BILLING_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_BILLING_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_BILLING_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_BILLING_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_BILLING_TYPES_PERMISSION);

        // payment types
        Permission::findOrCreate(AppPermissions::CREATE_PAYMENT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_PAYMENT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_PAYMENT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_PAYMENT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_PAYMENT_TYPES_PERMISSION);

        // property types
        Permission::findOrCreate(AppPermissions::CREATE_PROPERTY_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_PROPERTY_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_PROPERTY_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_PROPERTY_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_PROPERTY_TYPES_PERMISSION);

        // tenancy agreement types
        Permission::findOrCreate(AppPermissions::CREATE_TENANCY_AGREEMENT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_TENANCY_AGREEMENT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_TENANCY_AGREEMENT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_TENANCY_AGREEMENT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_TENANCY_AGREEMENT_TYPES_PERMISSION);

        // unit types
        Permission::findOrCreate(AppPermissions::CREATE_UNIT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_UNIT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_UNIT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_UNIT_TYPES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_UNIT_TYPES_PERMISSION);

        // tenancy bills
        Permission::findOrCreate(AppPermissions::CREATE_TENANCY_BILLS_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_TENANCY_BILLS_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_TENANCY_BILLS_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_TENANCY_BILLS_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_TENANCY_BILLS_PERMISSION);

        // meter readings
        Permission::findOrCreate(AppPermissions::CREATE_METER_READINGS_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_METER_READINGS_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_METER_READINGS_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_METER_READINGS_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_METER_READINGS_PERMISSION);

        // property services
        Permission::findOrCreate(AppPermissions::CREATE_PROPERTY_SERVICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_PROPERTY_SERVICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_PROPERTY_SERVICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_PROPERTY_SERVICES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_PROPERTY_SERVICES_PERMISSION);

        // property utilities
        Permission::findOrCreate(AppPermissions::CREATE_PROPERTY_UTILITIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::READ_PROPERTY_UTILITIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::UPDATE_PROPERTY_UTILITIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::DELETE_PROPERTY_UTILITIES_PERMISSION);
        Permission::findOrCreate(AppPermissions::RESTORE_PROPERTY_UTILITIES_PERMISSION);
    }
}
