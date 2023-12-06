<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Models\PropertyServices;
use App\Models\PropertyUtility;
use App\Models\TenancyAgreement;
use App\Models\Tenant;
use App\Models\Unit;
use Database\Seeders\PropertySeeder;
use Database\Seeders\PropertyServicesSeeder;
use Database\Seeders\PropertyUtilitySeeder;
use Database\Seeders\TenancyAgreementSeeder;
use Database\Seeders\TenantSeeder;
use Database\Seeders\UnitSeeder;
use Illuminate\Console\Command;

class InitDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-demo-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to initialize demo data on the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // delete all previous data for below tables
        TenancyAgreement::query()->forceDelete();
        PropertyServices::query()->forceDelete();
        PropertyUtility::query()->forceDelete();
        Tenant::query()->forceDelete();
        Unit::query()->forceDelete();
        Property::query()->forceDelete();

        // seed using new data
        $this->call(PropertySeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(TenantSeeder::class);
        $this->call(PropertyServicesSeeder::class);
        $this->call(PropertyUtilitySeeder::class);
        $this->call(TenancyAgreementSeeder::class);
    }
}
