<?php

namespace App\Models;

use App\Events\TenancyAgreementCreatedEvent;
use App\Utils\AppUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenancyAgreement extends DefaultAppModel
{

    protected $fillable = [
        'unit_id',
        'tenant_id',
        'agreement_type_id',
        'billing_type_id',
        'start_date',
        'end_date',
        'amount',
        'deposit_amount',
        'escalation_rate',
        'escalation_period_in_months',
        'next_escalation_date',
        'balance_carried_forward',
        'has_invoice_for_balance_carried_forward',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    protected $dispatchesEvents = [
        'created' => TenancyAgreementCreatedEvent::class, // dispatch this event once the tenancy agreement is created
    ];

    // foreign keys
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    public function tenancyBills()
    {
        return $this->hasMany(TenancyBill::class);
    }

    public function agreementType()
    {
        return $this->belongsTo(RefTenancyAgreementType::class);
    }

    public function billingType()
    {
        return $this->belongsTo(RefBillingType::class);
    }

    public function property()
    {
        return $this->hasOneThrough(
            Property::class,
            Unit::class,
            'id',
            'id',
            'unit_id',
            'property_id');
    }

    public function invoicePayments()
    {
//        return $this->hasMany(InvoicePayment::class);
        return $this->hasManyThrough(
            InvoicePayment::class,
            Invoice::class,
            'tenancy_agreement_id',
            'invoice_id',
            'id',
            'id'
        );
    }

    public function monthlyOccupationRecords()
    {
        return $this->hasMany(UnitOccupationMonthlyRecords::class, 'tenancy_agreement_id', 'id');
    }

    /**
     * Tenancy agreement files relationship
     */
    public function tenancyAgreementFiles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TenancyAgreementFiles::class);
    }

    public function escalationRateAmountsAndLogs()
    {
        return $this->hasMany(EscalationRatesAndAmountsLogs::class);
    }

    public function createInvoiceForBalanceCarriedForward()
    {
        // check whether the balance carried forward is greater than zero
        if ($this->balance_carried_forward <= 0){
            return [
                "status" => -1,
                "message" => "Balance carried forward is zero or less than zero. No invoice will be created."
            ];
        }
        // check if the invoice for balance carried forward exists
        if($this->has_invoice_for_balance_carried_forward){
            return [
                "status" => -1,
                "message" => "Invoice for balance carried forward already exists."
            ];
        }

        try {
            // create invoice for balance carried forward
            DB::transaction(function (){
                // create the manual invoice
                $manualInvoice = ManualInvoices::create([
                    'comments' => "Invoice for Balance Carried Forward",
                    'tenant_id' => $this->tenant_id,
                    'invoice_status' => 'unpaid',
                    'invoice_for_month' => now()->format('Y-m-d'),
                    'invoice_due_date' => Carbon::parse(now())->addDays(5)->format('Y-m-d'), // 5 days from now
                    'created_by' => auth()->user()->id,
                ]);

                // create the manual invoice item
                ManualInvoiceItem::create([
                    'manual_invoice_id' => $manualInvoice->id,
                    'name' => "Balance Carried Forward Invoice",
                    'bill_date' => now(),
                    'due_date' => Carbon::parse(now())->addDays(5)->format('Y-m-d'), // 5 days from now
                    'amount' => $this->balance_carried_forward,
                    'vat' => 0.0,
                    'total_amount' => $this->balance_carried_forward,
                    'billing_type_id' => $this->billing_type_id,
                    'category' => 'balance_carried_forward',
                    'created_by' => auth()->user()->id,
                ]);

                // update the tenancy agreement to reflect that the invoice for balance carried forward has been created
                $this->has_invoice_for_balance_carried_forward = true;
                $this->save();
            });
            return [
                "status" => 1,
                "message" => "Invoice for balance carried forward created successfully."
            ];
        } catch (\Exception $e) {
            Log::error("Error creating invoice for balance carried forward: ".$e->getMessage());
            return [
                "status" => -1,
                "message" => "Error creating invoice for balance carried forward."
            ];
        }
    }

    /**
     * Create deposit invoice
     * @param $billDate
     * @param $invoice
     * @return mixed
     * @throws \Exception
     *
     */
    public function createDepositInvoice(){
        \Log::info("Event listener reached for creating");

        $creationStatus = -1;

        DB::transaction(function () use ($creationStatus) {
            if ($this->deposit_amount == 0){
                return -1;
            }

            $billDate = new \DateTime(now());
            // ensure there is no bill for this month
            $tenancyBillExists = TenancyBill::query()
                ->where('tenancy_agreement_id', $this->id)
                ->where('service_id',null)
                ->where('utility_id',null)
                ->where('is_deposit',true)
                ->first();

            if ($tenancyBillExists){ // exit if the rent bill exists
                $creationStatus = $tenancyBillExists->id;
            }

            $invoice = new Invoice();

            $invoice->comments = "Invoice for Deposit amount";
            $invoice->tenancy_agreement_id = $this->id;
            $invoice->invoice_for_month = now()->format('Y-m-d');
            $invoice->invoice_due_date = Carbon::parse($this->created_at)->addDays(5)->format('Y-m-d'); // 5 days from tenancy agreement creation

            $invoice->save();

            // create tenancy Bill
            $tenancyBill = TenancyBill::create([
                'tenancy_agreement_id' => $this->id,
                'name' => $this->tenant->name.' Rent/Lease Deposit Bill',
                'bill_date' => now(),
                'due_date' => Carbon::parse($this->created_at)->addDays(5)->format('Y-m-d'), // 5 days from tenancy agreement creation
                'amount' => $this->deposit_amount,
                'vat' => 0.0,
                'total_amount' => $this->deposit_amount,
                'billing_type_id' => $this->billing_type_id,
                'is_deposit' => true,
                'invoice_id' => $invoice->id,
                'created_by' => $this->created_by,
            ]);

            $creationStatus = $tenancyBill->id;
        });


        return $creationStatus;
    }

    /*
     * Create the rental bill for the tenancy agreement
     */
    /**
     * @throws \Exception
     */
    public function createRentBill($billDate, $invoice){
        $billDate = new \DateTime($billDate);
        // ensure there is no bill for this month
        $tenancyBillExists = TenancyBill::query()
            ->where('tenancy_agreement_id', $this->id)
            ->where(function ($query) use ($billDate){
                // check if there is an escalation tied to this tenancy agreement id this month
                // if present, don't check for existence using the month
                // if not present, ensure no duplicates by checking bill date month
                EscalationRatesAndAmountsLogs::query()
                    ->where('tenancy_agreement_id',$this->id)
                    ->whereMonth('escalation_date',date_format($billDate,'m'))
                    ->exists()
                    ?
                    : $query->whereMonth('bill_date',date_format($billDate,'m'));
            })
            ->where('service_id',null)
            ->where('utility_id',null)
            ->where('is_deposit',false)
            ->first();

        if ($tenancyBillExists){ // exit if the rent bill exists
            return $tenancyBillExists->id;
        }

        // TODO: Extra check to prevent backdating of migrated users
        if ($billDate < new \DateTime('2024-04-01')){
            // check if the bill date is before this date (1st Feb, 2024)
            return -1;
        }

        // establish if unit is vatable
//        $isVatable = $this->unit->property->property_type_id == 1;
        if ($this->unit == null){
            Log::error("The tenancy agreement causing error: ".$this->id);
        }
        $isVatable = $this->unit->property->is_vatable;

        // define bill month name
//        $nextMonth =  $billDate->modify('+1 month')->format('F'); // TODO: FLAG:MIGRATION
// TODO: Ensure bill date is not modified, look for way to ensure safe triggering for invoice generation and bills
        $nextMonth =  $billDate->format('F');

        // create tenancy Bill
        $tenancyBill = TenancyBill::create([
            'tenancy_agreement_id' => $this->id,
            'name' => $nextMonth. ' Rent Bill',
//            'name' => $this->tenant->name.' '. $nextMonth. ' Rent Bill', TODO: FLAG:MIGRATION
//            'bill_date' => now(),
            'bill_date' => $billDate->format('Y-m-01'), // use the start of the month as the bill date
            'due_date' => // this month 5th
                date_format(
                    date_create(date_format($billDate,'Y-m-d')),
                    'Y-m-5'
                ),
            'amount' => $this->amount,
            'vat' => $isVatable ? $this->amount * AppUtils::VAT_RATE : 0.0,
            'total_amount' => $this->amount + ($isVatable ? $this->amount * AppUtils::VAT_RATE : 0.0),
            'billing_type_id' => $this->billing_type_id,
            'invoice_id' => $invoice->id,
            'created_by' => auth()->user()->id,
        ]);

        return $tenancyBill->id;
    }

    public function createServiceBill($billDate,$invoice)
    {
        $billDate = new \DateTime($billDate); // TODO: FLAG:MIGRATION All bills are generated past first of the month
        if ($billDate < new \DateTime('2024-04-01')){
            return;
        }
        // get the various services within this property
        // generate a bill for each for this month, for this tenancy agreement
        $this->property->propertyServices()->get()->each(/**
         * @throws \Exception
         */ function ($service) use ($invoice,$billDate){
            // ensure service bill does not exist for the given month
            $serviceBillExists = TenancyBill::query()
                ->where('tenancy_agreement_id', $this->id)
                ->whereMonth('bill_date', '=', $billDate->format('m'))
                ->whereYear('bill_date', trim(date_format($billDate,'Y')))
                ->where('service_id',$service->service_id)
                ->exists();

            // TODO: Extra check to prevent backdating of migrated users

            if (!$serviceBillExists) {// exit if the service bill exists
                // establish if property is vatable
                $isVatable = $this->property->property_type_id == 1;
                // create service bill
                $billDueDate = $billDate;

                $isServiceAreaBased = Services::query()
                    ->where('id','=',$service->service_id)
                    ->value('is_area_based_service');

                if ($isServiceAreaBased){
                    // get the area of the unit
                    $unitArea = $this->unit->area_in_square_feet;
                    // get the rate of the service
                    $serviceRate = $service->rate;
                    // calculate the amount
                    $serviceAmount = $unitArea * $serviceRate;
                    $serviceVat = $serviceAmount * AppUtils::VAT_RATE;
                }else{
                    $serviceAmount = $service->rate;
                    $serviceVat = $isVatable ? $serviceAmount * AppUtils::VAT_RATE : 0.0;
                }

                $serviceTotalAmount = $serviceAmount + $serviceVat;

                TenancyBill::create([
                    'tenancy_agreement_id' => $this->id,
//                    'name' => $this->tenant->name.' '. TODO: FLAG:MIGRATION removed unnecessary tenant name
                    'name' => $billDate->format('F'). ' '.
                        Services::query()->where('id','=',$service->service_id)->value('name').
                        ' Service Bill',
//                    'bill_date' => now(),
                    'bill_date' => $billDate->format('Y-m-01'), // use the start of the month as the bill date
                    'due_date' => $billDueDate->format('Y-m-5'),
//                    'amount' => $service->rate,
//                    'vat' => $isVatable ? $service->rate * AppUtils::VAT_RATE : 0.0,
//                    'total_amount' => $service->rate + ($isVatable ? $service->rate * AppUtils::VAT_RATE : 0.0),
                    'amount' => $serviceAmount,
                    'vat' => $serviceVat,
                    'total_amount' => $serviceTotalAmount,
                    'billing_type_id' => $service->billing_type_id,
                    'service_id' => $service->service_id,
                    'invoice_id' => $invoice->id,
                    'created_by' => auth()->user()->id
                ]);
            }
        });
    }

    public function createUnitOccupationMonthlyRecord($billDate,$tenancyBillId)
    {
        $billDate = new \DateTime($billDate);
        $unitOccupationMonthlyRecord = new UnitOccupationMonthlyRecords();

        $unitOccupationMonthlyRecord->unit_id = $this->unit_id;
        $unitOccupationMonthlyRecord->tenancy_agreement_id = $this->id;
        $unitOccupationMonthlyRecord->from_date = // check if the start date is after first day of the month
            $this->start_date > date_format($billDate,'Y-m-01')
                ? $this->start_date
                : date_format($billDate,'Y-m-01');
        // check if end date is null
        // if not null, check if it is before the last day of the month
        $endDate = $this->end_date ?? date_format($billDate,'Y-m-t');
//        $unitOccupationMonthlyRecord->end_date = $this->end_date < date_format($billDate,'Y-m-t')
//            ? $this->end_date
//            : date_format($billDate,'Y-m-t');
        $unitOccupationMonthlyRecord->end_date = $endDate < date_format($billDate,'Y-m-t')
            ? $this->end_date
            : date_format($billDate,'Y-m-t');
        $unitOccupationMonthlyRecord->tenancy_bill_id = $tenancyBillId;
        $unitOccupationMonthlyRecord->created_by = auth()->user()->id;

        $unitOccupationMonthlyRecord->save();
    }
}
