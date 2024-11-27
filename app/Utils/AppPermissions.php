<?php

namespace App\Utils;

class AppPermissions
{
    // roles permissions
    const CREATE_ROLES_PERMISSION = 'create-roles';
    const READ_ROLES_PERMISSION = 'read-roles';
    const UPDATE_ROLES_PERMISSION = 'update-roles';
    const DELETE_ROLES_PERMISSION = 'delete-roles';
    const RESTORE_ROLES_PERMISSION = 'restore-roles';

    // credit notes
    const CREATE_CREDIT_NOTES_PERMISSION = 'create-credit-notes';
    const READ_CREDIT_NOTES_PERMISSION = 'read-credit-notes';
    const UPDATE_CREDIT_NOTES_PERMISSION = 'update-credit-notes';
    const DELETE_CREDIT_NOTES_PERMISSION = 'delete-credit-notes';
    const RESTORE_CREDIT_NOTES_PERMISSION = 'restore-credit-notes';

    // invoices
    const CREATE_INVOICES_PERMISSION = 'create-invoices';
    const READ_INVOICES_PERMISSION = 'read-invoices';
    const UPDATE_INVOICES_PERMISSION = 'update-invoices';
    const DELETE_INVOICES_PERMISSION = 'delete-invoices';
    const RESTORE_INVOICES_PERMISSION = 'restore-invoices';

    // properties
    const CREATE_PROPERTIES_PERMISSION = 'create-properties';
    const READ_PROPERTIES_PERMISSION = 'read-properties';
    const UPDATE_PROPERTIES_PERMISSION = 'update-properties';
    const DELETE_PROPERTIES_PERMISSION = 'delete-properties';
    const RESTORE_PROPERTIES_PERMISSION = 'restore-properties';

    // units
    const CREATE_UNITS_PERMISSION = 'create-units';
    const READ_UNITS_PERMISSION = 'read-units';
    const UPDATE_UNITS_PERMISSION = 'update-units';
    const DELETE_UNITS_PERMISSION = 'delete-units';
    const RESTORE_UNITS_PERMISSION = 'restore-units';

    // rent payments
    const CREATE_RENT_PAYMENTS_PERMISSION = 'create-rent-payments';
    const READ_RENT_PAYMENTS_PERMISSION = 'read-rent-payments';
    const UPDATE_RENT_PAYMENTS_PERMISSION = 'update-rent-payments';
    const DELETE_RENT_PAYMENTS_PERMISSION = 'delete-rent-payments';
    const RESTORE_RENT_PAYMENTS_PERMISSION = 'restore-rent-payments';

    // services
    const CREATE_SERVICES_PERMISSION = 'create-services';
    const READ_SERVICES_PERMISSION = 'read-services';
    const UPDATE_SERVICES_PERMISSION = 'update-services';
    const DELETE_SERVICES_PERMISSION = 'delete-services';
    const RESTORE_SERVICES_PERMISSION = 'restore-services';

    // tenants
    const CREATE_TENANTS_PERMISSION = 'create-tenants';
    const READ_TENANTS_PERMISSION = 'read-tenants';
    const UPDATE_TENANTS_PERMISSION = 'update-tenants';
    const DELETE_TENANTS_PERMISSION = 'delete-tenants';
    const RESTORE_TENANTS_PERMISSION = 'restore-tenants';

    // users
    const CREATE_USERS_PERMISSION = 'create-users';
    const READ_USERS_PERMISSION = 'read-users';
    const UPDATE_USERS_PERMISSION = 'update-users';
    const DELETE_USERS_PERMISSION = 'delete-users';
    const RESTORE_USERS_PERMISSION = 'restore-users';

    // utilities
    const CREATE_UTILITIES_PERMISSION = 'create-utilities';
    const READ_UTILITIES_PERMISSION = 'read-utilities';
    const UPDATE_UTILITIES_PERMISSION = 'update-utilities';
    const DELETE_UTILITIES_PERMISSION = 'delete-utilities';
    const RESTORE_UTILITIES_PERMISSION = 'restore-utilities';

    // vacation notices
    const CREATE_VACATION_NOTICES_PERMISSION = 'create-vacation-notices';
    const READ_VACATION_NOTICES_PERMISSION = 'read-vacation-notices';
    const UPDATE_VACATION_NOTICES_PERMISSION = 'update-vacation-notices';
    const DELETE_VACATION_NOTICES_PERMISSION = 'delete-vacation-notices';
    const RESTORE_VACATION_NOTICES_PERMISSION = 'restore-vacation-notices';

    // billing types
    const CREATE_BILLING_TYPES_PERMISSION = 'create-billing-types';
    const READ_BILLING_TYPES_PERMISSION = 'read-billing-types';
    const UPDATE_BILLING_TYPES_PERMISSION = 'update-billing-types';
    const DELETE_BILLING_TYPES_PERMISSION = 'delete-billing-types';
    const RESTORE_BILLING_TYPES_PERMISSION = 'restore-billing-types';

    // payment types
    const CREATE_PAYMENT_TYPES_PERMISSION = 'create-payment-types';
    const READ_PAYMENT_TYPES_PERMISSION = 'read-payment-types';
    const UPDATE_PAYMENT_TYPES_PERMISSION = 'update-payment-types';
    const DELETE_PAYMENT_TYPES_PERMISSION = 'delete-payment-types';
    const RESTORE_PAYMENT_TYPES_PERMISSION = 'restore-payment-types';

    // property types
    const CREATE_PROPERTY_TYPES_PERMISSION = 'create-property-types';
    const READ_PROPERTY_TYPES_PERMISSION = 'read-property-types';
    const UPDATE_PROPERTY_TYPES_PERMISSION = 'update-property-types';
    const DELETE_PROPERTY_TYPES_PERMISSION = 'delete-property-types';
    const RESTORE_PROPERTY_TYPES_PERMISSION = 'restore-property-types';

    // tenancy agreement types
    const CREATE_TENANCY_AGREEMENT_TYPES_PERMISSION = 'create-tenancy-agreement-types';
    const READ_TENANCY_AGREEMENT_TYPES_PERMISSION = 'read-tenancy-agreement-types';
    const UPDATE_TENANCY_AGREEMENT_TYPES_PERMISSION = 'update-tenancy-agreement-types';
    const DELETE_TENANCY_AGREEMENT_TYPES_PERMISSION = 'delete-tenancy-agreement-types';
    const RESTORE_TENANCY_AGREEMENT_TYPES_PERMISSION = 'restore-tenancy-agreement-types';

    // unit types
    const CREATE_UNIT_TYPES_PERMISSION = 'create-unit-types';
    const READ_UNIT_TYPES_PERMISSION = 'read-unit-types';
    const UPDATE_UNIT_TYPES_PERMISSION = 'update-unit-types';
    const DELETE_UNIT_TYPES_PERMISSION = 'delete-unit-types';
    const RESTORE_UNIT_TYPES_PERMISSION = 'restore-unit-types';

    // tenancy bills
    const CREATE_TENANCY_BILLS_PERMISSION = 'create-tenancy-bills';
    const READ_TENANCY_BILLS_PERMISSION = 'read-tenancy-bills';
    const UPDATE_TENANCY_BILLS_PERMISSION = 'update-tenancy-bills';
    const DELETE_TENANCY_BILLS_PERMISSION = 'delete-tenancy-bills';
    const RESTORE_TENANCY_BILLS_PERMISSION = 'restore-tenancy-bills';

    // meter readings
    const CREATE_METER_READINGS_PERMISSION = 'create-meter-readings';
    const READ_METER_READINGS_PERMISSION = 'read-meter-readings';
    const UPDATE_METER_READINGS_PERMISSION = 'update-meter-readings';
    const DELETE_METER_READINGS_PERMISSION = 'delete-meter-readings';
    const RESTORE_METER_READINGS_PERMISSION = 'restore-meter-readings';

    // property services permissions
    const CREATE_PROPERTY_SERVICES_PERMISSION = 'create-property-services';
    const READ_PROPERTY_SERVICES_PERMISSION = 'read-property-services';
    const UPDATE_PROPERTY_SERVICES_PERMISSION = 'update-property-services';
    const DELETE_PROPERTY_SERVICES_PERMISSION = 'delete-property-services';
    const RESTORE_PROPERTY_SERVICES_PERMISSION = 'restore-property-services';

    // property utilities permissions
    const CREATE_PROPERTY_UTILITIES_PERMISSION = 'create-property-utilities';
    const READ_PROPERTY_UTILITIES_PERMISSION = 'read-property-utilities';
    const UPDATE_PROPERTY_UTILITIES_PERMISSION = 'update-property-utilities';
    const DELETE_PROPERTY_UTILITIES_PERMISSION = 'delete-property-utilities';
    const RESTORE_PROPERTY_UTILITIES_PERMISSION = 'restore-property-utilities';

    // tenancy agreements
    const CREATE_TENANCY_AGREEMENTS_PERMISSION = 'create-tenancy-agreements';
    const READ_TENANCY_AGREEMENTS_PERMISSION = 'read-tenancy-agreements';
    const UPDATE_TENANCY_AGREEMENTS_PERMISSION = 'update-tenancy-agreements';
    const DELETE_TENANCY_AGREEMENTS_PERMISSION = 'delete-tenancy-agreements';
    const RESTORE_TENANCY_AGREEMENTS_PERMISSION = 'restore-tenancy-agreements';

    // invoice payments
    const CREATE_INVOICE_PAYMENTS_PERMISSION = 'create-invoice-payments';
    const READ_INVOICE_PAYMENTS_PERMISSION = 'read-invoice-payments';
    const UPDATE_INVOICE_PAYMENTS_PERMISSION = 'update-invoice-payments';
    const DELETE_INVOICE_PAYMENTS_PERMISSION = 'delete-invoice-payments';
    const RESTORE_INVOICE_PAYMENTS_PERMISSION = 'restore-invoice-payments';

    // unit occupation monthly records
    const CREATE_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION = 'create-unit-occupation-monthly-records';
    const READ_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION = 'read-unit-occupation-monthly-records';
    const UPDATE_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION = 'update-unit-occupation-monthly-records';
    const DELETE_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION = 'delete-unit-occupation-monthly-records';
    const RESTORE_UNIT_OCCUPATION_MONTHLY_RECORDS_PERMISSION = 'restore-unit-occupation-monthly-records';

    // sent emails
    const CREATE_SENT_EMAILS_PERMISSION = 'create-sent-emails';
    const READ_SENT_EMAILS_PERMISSION = 'read-sent-emails';
    const UPDATE_SENT_EMAILS_PERMISSION = 'update-sent-emails';
    const DELETE_SENT_EMAILS_PERMISSION = 'delete-sent-emails';
    const RESTORE_SENT_EMAILS_PERMISSION = 'restore-sent-emails';

    // email attachments
    const CREATE_EMAIL_ATTACHMENTS_PERMISSION = 'create-email-attachments';
    const READ_EMAIL_ATTACHMENTS_PERMISSION = 'read-email-attachments';
    const UPDATE_EMAIL_ATTACHMENTS_PERMISSION = 'update-email-attachments';
    const DELETE_EMAIL_ATTACHMENTS_PERMISSION = 'delete-email-attachments';
    const RESTORE_EMAIL_ATTACHMENTS_PERMISSION = 'restore-email-attachments';

    // company details permissions
    const CREATE_COMPANY_DETAILS_PERMISSION = 'create-company-details';
    const READ_COMPANY_DETAILS_PERMISSION = 'read-company-details';
    const UPDATE_COMPANY_DETAILS_PERMISSION = 'update-company-details';
    const DELETE_COMPANY_DETAILS_PERMISSION = 'delete-company-details';
    const RESTORE_COMPANY_DETAILS_PERMISSION = 'restore-company-details';

    // pdfs
    const GENERATE_PDF_FILE_PERMISSION = 'generate-pdf-file';
    
    // csv
    const GENERATE_CSV_FILE_PERMISSION = 'generate-csv-file';

    // property management users permissions
    const CREATE_PROPERTY_MANAGEMENT_USERS_PERMISSION = 'create-property-management-users';
    const READ_PROPERTY_MANAGEMENT_USERS_PERMISSION = 'read-property-management-users';
    const UPDATE_PROPERTY_MANAGEMENT_USERS_PERMISSION = 'update-property-management-users';
    const DELETE_PROPERTY_MANAGEMENT_USERS_PERMISSION = 'delete-property-management-users';
    const RESTORE_PROPERTY_MANAGEMENT_USERS_PERMISSION = 'restore-property-management-users';

    // audits permissions
    const CREATE_AUDITS_PERMISSION = 'create-audits';
    const READ_AUDITS_PERMISSION = 'read-audits';
    const UPDATE_AUDITS_PERMISSION = 'update-audits';
    const DELETE_AUDITS_PERMISSION = 'delete-audits';
    const RESTORE_AUDITS_PERMISSION = 'restore-audits';

        // property
        const GENERATE_PROPERTY_PDF = 'generate_property_pdf';

        // invoice
        const GENERATE_INVOICE_PDF = 'generate_invoice_pdf';
    
        // client
        const GENERATE_CLIENT_PDF = 'generate_client_pdf';
    
        // tenant
        const GENERATE_TENANT_PDF = 'generate_tenant_pdf';
    
        // manual invoice
        const GENERATE_MANUAL_INVOICE_PDF = 'generate_manual_invoice_pdf';
    
        // invoice payment
        const GENERATE_INVOICE_PAYMENT_PDF = 'generate_invoice_payment_pdf';
    
        // property owner
        const GENERATE_PROPERTY_OWNER_PDF = 'generate_property_owner_pdf';
}
