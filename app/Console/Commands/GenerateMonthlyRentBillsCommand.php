<?php

namespace App\Console\Commands;

use App\Models\TenancyAgreement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyRentBillsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-monthly-rent-bills-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly rent bills command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        // get all tenancy agreements that started lasted last month and create bills
        TenancyAgreement::query()
            ->where(function($query){
                $query->where('end_date', '>=', now()->subMonth()->endOfMonth())
                    ->orWhereDate('end_date', null);
            })
            ->select('id', 'unit_id', 'tenant_id', 'agreement_type_id','billing_type_id','start_date', 'end_date','amount','created_at')
            ->orderBy('start_date', 'asc')
            ->chunk(100, function ($tenancyAgreements) {
                Log::info('Generating bills for tenancy agreements'. $tenancyAgreements->count());
                foreach ($tenancyAgreements as $tenancyAgreement) {
                    $tenancyAgreement->createRentBill();
                    $tenancyAgreement->createServiceBill();
                }
            });
    }
}
