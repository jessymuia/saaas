<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Services;
use App\Models\TenancyAgreement;
use App\Models\TenancyBill;
use App\Utils\AppUtils;
use Illuminate\Console\Command;

class ExclusiveGarbageFeeCharging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:exclusive-garbage-fee-charging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Garbage fee charging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        // exclusive garbage fee charges
        $billDate = "2024-05-01";
        $billDate = new \DateTime($billDate); // TODO: FLAG:MIGRATION All bills are generated past first of the month
        if ($billDate < new \DateTime('2024-04-01')){
            return;
        }
        // get all tenancy agreements for a given property id
        $tenancyAgreements = TenancyAgreement::query()
            ->whereHas('property',function ($query) {
                $query->where('properties.id', 3);// id for Wanjeri Court
            })
            ->where('status',1)
            ->where(function ($query) use ($billDate){
                $query->where('end_date', '>=', $billDate->format('Y-m-d'))
                    ->orWhereNull('end_date');
            })
            ->whereHas('unit',function ($query){
                $query->whereNotIn('units.name',["WG02","WG03","WG04","WG05","WG06","WG07","WG08","WG09","WG10"]);
            })
            ->get();

        // display on terminal number of tenancy agreements in this property
        $this->info("Number of tenancy agreements: ". $tenancyAgreements->count());

        foreach ($tenancyAgreements as $tenancyAgreement){
            $invoice = Invoice::query()
                ->where('tenancy_agreement_id', $tenancyAgreement->id)
                ->whereHas('tenancyBills',function ($query) use ($billDate){
                    $query->where('bill_date', $billDate->format('Y-m-01'));
                })
                ->whereMonth('invoice_for_month', date_format($billDate,'m')) // TODO: FLAG:MIGRATION
//                ->where('is_confirmed',0)
//                ->where('is_generated',0)
                ->orderBy('id','desc') // get the latest invoice
                ->first();

            if (!$invoice){
                continue;
            }

            // display on terminal the id of hte invoice for this tenancy agreement
            $this->info("Invoice id: ". $invoice->id);

//            // get the various services within this property
//            // generate a bill for each for this month, for this tenancy agreement
//            $tenancyAgreement->property->propertyServices()->get()->each(/**
//             * @throws \Exception
//             */ function ($service) use ($invoice,$billDate,$tenancyAgreement) {
//                // ensure service bill does not exist for the given month
//                $serviceBillExists = TenancyBill::query()
//                    ->where('tenancy_agreement_id', $this->id)
//                    ->whereMonth('bill_date', '=', $billDate->format('m'))
//                    ->whereYear('bill_date', trim(date_format($billDate,'Y')))
//                    ->where('service_id',$service->service_id)
//                    ->exists();
//
//                // TODO: Extra check to prevent backdating of migrated users
//
//                if (!$serviceBillExists) {// exit if the service bill exists
//                    // establish if property is vatable
//                    $isVatable = $tenancyAgreement->property->property_type_id == 1;
//                    // create service bill
//                    $billDueDate = $billDate;
//
//                    $isServiceAreaBased = Services::query()
//                        ->where('id','=',$service->service_id)
//                        ->value('is_area_based_service');
//
//                    if ($isServiceAreaBased){
//                        // get the area of the unit
//                        $unitArea = $tenancyAgreement->unit->area_in_square_feet;
//                        // get the rate of the service
//                        $serviceRate = $service->rate;
//                        // calculate the amount
//                        $serviceAmount = $unitArea * $serviceRate;
//                        $serviceVat = $serviceAmount * AppUtils::VAT_RATE;
//                    }else{
//                        $serviceAmount = $service->rate;
//                        $serviceVat = $isVatable ? $serviceAmount * AppUtils::VAT_RATE : 0.0;
//                    }
//
//                    $serviceTotalAmount = $serviceAmount + $serviceVat;
//
//                    TenancyBill::create([
//                        'tenancy_agreement_id' => $this->id,
////                    'name' => $this->tenant->name.' '. TODO: FLAG:MIGRATION removed unnecessary tenant name
//                        'name' => $billDate->format('F'). ' '.
//                            Services::query()->where('id','=',$service->service_id)->value('name').
//                            ' Service Bill',
////                    'bill_date' => now(),
//                        'bill_date' => $billDate->format('Y-m-01'), // use the start of the month as the bill date
//                        'due_date' => $billDueDate->format('Y-m-5'),
////                    'amount' => $service->rate,
////                    'vat' => $isVatable ? $service->rate * AppUtils::VAT_RATE : 0.0,
////                    'total_amount' => $service->rate + ($isVatable ? $service->rate * AppUtils::VAT_RATE : 0.0),
//                        'amount' => $serviceAmount,
//                        'vat' => $serviceVat,
//                        'total_amount' => $serviceTotalAmount,
//                        'billing_type_id' => $service->billing_type_id,
//                        'service_id' => $service->service_id,
//                        'invoice_id' => $invoice->id,
//                        'created_by' => auth()->user()->id
//                    ]);
//                }
//            });
        }
    }
}
