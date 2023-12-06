<?php

namespace Database\Seeders;

use App\Models\RefBillingType;
use App\Models\TenancyAgreement;
use App\Models\Tenant;
use App\Models\Unit;
use Database\Factories\TenancyAgreementFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenancyAgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        for ($i = 0;$i < 200;$i++){
            TenancyAgreementFactory::new()->create();
        }
    }
}
