<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RefDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPropertyTypes();
        $this->seedBillingTypes();
        $this->seedUnitTypes();
        $this->seedPaymentTypes();
        $this->seedTenancyAgreementTypes();
        $this->seedUtilities();
        $this->seedServices();
    }

    private function seedPropertyTypes(): void
    {
        $rows = [
            ['type' => 'Residential', 'description' => 'Homes, apartments and residential units for renting.'],
            ['type' => 'Commercial',  'description' => 'Office spaces, shops and business premises.'],
            ['type' => 'Industrial',  'description' => 'Warehouses, factories and light-industrial units.'],
            ['type' => 'Mixed Use',   'description' => 'Properties combining residential and commercial use.'],
            ['type' => 'Land',        'description' => 'Bare land or plots without structures.'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('ref_property_types')->where('type', $row['type'])->exists();
            if (!$exists) {
                DB::table('ref_property_types')->insert(array_merge($row, $this->defaults()));
            }
        }

        $this->command->info('ref_property_types seeded: ' . count($rows) . ' types');
    }

    private function seedBillingTypes(): void
    {
        $rows = [
            ['type' => 'Monthly',    'description' => 'Billed every month.',   'frequency_months' => 1,  'due_day' => 1],
            ['type' => 'Quarterly',  'description' => 'Billed every quarter.',  'frequency_months' => 3,  'due_day' => 1],
            ['type' => 'Bi-Annual',  'description' => 'Billed twice a year.',   'frequency_months' => 6,  'due_day' => 1],
            ['type' => 'Annual',     'description' => 'Billed once a year.',    'frequency_months' => 12, 'due_day' => 1],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('ref_billing_types')->where('type', $row['type'])->exists();
            if (!$exists) {
                DB::table('ref_billing_types')->insert(array_merge($row, $this->defaults()));
            }
        }

        $this->command->info('ref_billing_types seeded: ' . count($rows) . ' types');
    }

    private function seedUnitTypes(): void
    {
        $rows = [
            ['name' => 'Bedsitter',     'code' => 'BS'],
            ['name' => 'Single Room',   'code' => 'SR'],
            ['name' => '1 Bedroom',     'code' => '1B'],
            ['name' => '2 Bedroom',     'code' => '2B'],
            ['name' => '3 Bedroom',     'code' => '3B'],
            ['name' => '4 Bedroom',     'code' => '4B'],
            ['name' => 'Penthouse',     'code' => 'PH'],
            ['name' => 'Studio',        'code' => 'ST'],
            ['name' => 'Office Space',  'code' => 'OS'],
            ['name' => 'Retail Shop',   'code' => 'RS'],
            ['name' => 'Warehouse',     'code' => 'WH'],
            ['name' => 'Parking Bay',   'code' => 'PB'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('ref_unit_types')->where('name', $row['name'])->exists();
            if (!$exists) {
                DB::table('ref_unit_types')->insert(array_merge($row, $this->defaults()));
            }
        }

        $this->command->info('ref_unit_types seeded: ' . count($rows) . ' types');
    }

    private function seedPaymentTypes(): void
    {
        $rows = [
            ['type' => 'M-Pesa',          'description' => 'Mobile money payment via Safaricom M-Pesa.'],
            ['type' => 'Bank Transfer',    'description' => 'Direct transfer from tenant bank account.'],
            ['type' => 'Cash',             'description' => 'Physical cash payment collected by caretaker.'],
            ['type' => 'Cheque',           'description' => 'Payment by bank cheque.'],
            ['type' => 'Airtel Money',     'description' => 'Mobile money via Airtel Money.'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('ref_payment_types')->where('type', $row['type'])->exists();
            if (!$exists) {
                DB::table('ref_payment_types')->insert(array_merge($row, $this->defaults()));
            }
        }

        $this->command->info('ref_payment_types seeded: ' . count($rows) . ' types');
    }

    private function seedTenancyAgreementTypes(): void
    {
        $rows = [
            ['type' => 'Fixed Term',    'description' => 'Lease for a defined period, e.g. 6 or 12 months.'],
            ['type' => 'Periodic',      'description' => 'Rolling month-to-month or week-to-week agreement.'],
            ['type' => 'Short-term',    'description' => 'Agreement for short stays under 3 months.'],
            ['type' => 'Commercial',    'description' => 'Agreement for commercial or office tenancies.'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('ref_tenancy_agreement_types')->where('type', $row['type'])->exists();
            if (!$exists) {
                DB::table('ref_tenancy_agreement_types')->insert(array_merge($row, $this->defaults()));
            }
        }

        $this->command->info('ref_tenancy_agreement_types seeded: ' . count($rows) . ' types');
    }

    private function seedUtilities(): void
    {
        $rows = [
            ['name' => 'Water',       'description' => 'Municipal or borehole water supply.',   'unit_of_measurement' => 'm³'],
            ['name' => 'Electricity', 'description' => 'Kenya Power (KPLC) electricity supply.','unit_of_measurement' => 'kWh'],
            ['name' => 'Gas',         'description' => 'LPG or piped natural gas.',              'unit_of_measurement' => 'kg'],
            ['name' => 'Internet',    'description' => 'Fibre or broadband internet service.',   'unit_of_measurement' => 'Mbps'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('ref_utilities')->where('name', $row['name'])->exists();
            if (!$exists) {
                DB::table('ref_utilities')->insert(array_merge($row, $this->defaults()));
            }
        }

        $this->command->info('ref_utilities seeded: ' . count($rows) . ' utilities');
    }

    private function seedServices(): void
    {
        $rows = [
            ['name' => 'Garbage Collection', 'description' => 'Monthly waste removal service.',                   'is_area_based_service' => false],
            ['name' => 'Security',           'description' => 'Compound security guard service.',                 'is_area_based_service' => false],
            ['name' => 'Cleaning',           'description' => 'Common area and staircase cleaning.',              'is_area_based_service' => false],
            ['name' => 'Parking',            'description' => 'Dedicated parking bay allocation.',                'is_area_based_service' => false],
            ['name' => 'Gym',                'description' => 'Shared gym and fitness centre access.',            'is_area_based_service' => false],
            ['name' => 'Swimming Pool',      'description' => 'Shared pool maintenance and access.',              'is_area_based_service' => false],
            ['name' => 'Garden Maintenance', 'description' => 'Landscaping and green-area upkeep.',               'is_area_based_service' => true],
            ['name' => 'Pest Control',       'description' => 'Scheduled fumigation and pest management.',        'is_area_based_service' => false],
            ['name' => 'Elevator',           'description' => 'Lift maintenance and service charge.',             'is_area_based_service' => false],
            ['name' => 'Concierge',          'description' => 'Concierge and front-desk reception service.',      'is_area_based_service' => false],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('services')->where('name', $row['name'])->exists();
            if (!$exists) {
                DB::table('services')->insert(array_merge($row, $this->defaults()));
            }
        }

        $this->command->info('services seeded: ' . count($rows) . ' services');
    }

    private function defaults(): array
    {
        return [
            'id'         => (string) Str::uuid(),
            'version'    => 1.0,
            'status'     => true,
            'archive'    => false,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
