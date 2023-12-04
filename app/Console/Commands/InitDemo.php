<?php

namespace App\Console\Commands;

use App\Models\RefBillingType;
use App\Models\RefPaymentType;
use App\Models\RefPropertyType;
use App\Models\RefTenancyAgreementType;
use App\Models\RefUnitType;
use App\Models\RefUtility;
use App\Models\Services;
use Illuminate\Console\Command;

class InitDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize demo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // billing type
        $billingType = new RefBillingType();
        $billingType->type = 'Monthly';
        $billingType->description = 'Monthly';
        $billingType->frequency_months = 1;
        $billingType->due_day = 25;

        $billingType->save();

        $billingType = new RefBillingType();
        $billingType->type = 'Quarterly';
        $billingType->description = 'Quarterly';
        $billingType->frequency_months = 3;
        $billingType->due_day = 25;

        $billingType->save();

        $billingType = new RefBillingType();
        $billingType->type = 'Semi-Annually';
        $billingType->description = 'Semi-Annually';
        $billingType->frequency_months = 6;
        $billingType->due_day = 25;

        $billingType->save();

        // confirm that the number of billing types is 3
        RefBillingType::all()->count() != 3
            ? $this->error('Error creating billing types')
            : $this->info('Billing types created successfully');

        // payment type
        $paymentType = new RefPaymentType();
        $paymentType->type = 'Cash';
        $paymentType->description = 'Cash';

        $paymentType->save();

        $paymentType = new RefPaymentType();
        $paymentType->type = 'Mpesa';
        $paymentType->description = 'Mpesa';

        $paymentType->save();

        $paymentType = new RefPaymentType();
        $paymentType->type = 'Check';
        $paymentType->description = 'Check';

        $paymentType->save();

        $paymentType = new RefPaymentType();
        $paymentType->type = 'Bank Transfer';
        $paymentType->description = 'Bank Transfer';

        $paymentType->save();

        // confirm that the number of payment types is 4
        RefPaymentType::all()->count() != 4
            ? $this->error('Error creating payment types')
            : $this->info('Payment types created successfully');

        // property type
//        $propertyType = new RefPropertyType();
//        $propertyType->type = "Industrial";
//        $propertyType->description = "Industrial";
//
//        $propertyType->save();

        $propertyType = new RefPropertyType();
        $propertyType->type = "Commercial";
        $propertyType->description = "Commercial";

        $propertyType->save();

        $propertyType = new RefPropertyType();
        $propertyType->type = "Residential";
        $propertyType->description = "Residential";

        $propertyType->save();

        // confirm that the number of property types is 3
        RefPropertyType::all()->count() != 3
            ? $this->error('Error creating property types')
            : $this->info('Property types created successfully');

        // tenancy agreement type
        $tenancyAgreementType = new RefTenancyAgreementType();
        $tenancyAgreementType->type = "Fixed-term tenancy";
        $tenancyAgreementType->description = "Fixed-term tenancy";

        $tenancyAgreementType->save();

        $tenancyAgreementType = new RefTenancyAgreementType();
        $tenancyAgreementType->type = "Month-to-month tenancy";
        $tenancyAgreementType->description = "Month-to-month tenancy";

        $tenancyAgreementType->save();

        // confirm that the number of tenancy agreement types is 2
        RefTenancyAgreementType::all()->count() != 2
            ? $this->error('Error creating tenancy agreement types')
            : $this->info('Tenancy agreement types created successfully');

        // unit type
        $unitType = new RefUnitType();
        $unitType->type = "Bedsitter";
        $unitType->description = "Bedsitter";

        $unitType->save();

        $unitType = new RefUnitType();
        $unitType->type = "1 Bedroom";
        $unitType->description = "1 Bedroom";

        $unitType->save();

        $unitType = new RefUnitType();
        $unitType->type = "2 Bedroom";
        $unitType->description = "2 Bedroom";

        $unitType->save();

        $unitType = new RefUnitType();
        $unitType->type = "3 Bedroom";
        $unitType->description = "3 Bedroom";

        $unitType->save();

        // confirm that the number of unit types is 4
        RefUnitType::all()->count() != 4
            ? $this->error('Error creating unit types')
            : $this->info('Unit types created successfully');

        // utilities
        $utility = new RefUtility();
        $utility->name = "Water";
        $utility->description = "Water";
        $utility->unit_of_measurement = "m3";

        $utility->save();

        $utility = new RefUtility();
        $utility->name = "Electricity";
        $utility->description = "Electricity";
        $utility->unit_of_measurement = "kWh";

        $utility->save();

        $utility = new RefUtility();
        $utility->name = "Gas";
        $utility->description = "Gas";
        $utility->unit_of_measurement = "m3";

        $utility->save();

        // confirm that the number of utilities is 3
        RefUtility::all()->count() != 3
            ? $this->error('Error creating utilities')
            : $this->info('Utilities created successfully');

        // services
        $service = new Services();

        $service->name = "Cleaning";
        $service->description = "Cleaning";

        $service->save();

        $service = new Services();

        $service->name = "Security";
        $service->description = "Security";

        $service->save();

        $service = new Services();

        $service->name = "Garbage";
        $service->description = "Garbage";

        $service->save();

        // confirm that the number of services is 3
        Services::all()->count() != 3
            ? $this->error('Error creating services')
            : $this->info('Services created successfully');
    }
}
