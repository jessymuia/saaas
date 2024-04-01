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
//        'balance_carried_forward', TODO: Uncomment FLAG:MIGRATION
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
        if ($billDate < new \DateTime('2024-03-01')){
            // check if the bill date is before this date (1st Feb, 2024)
            return -1;
        }

        // establish if unit is vatable
        $isVatable = $this->unit->property->property_type_id == 1;

        // define bill month name
        $nextMonth =  $billDate->modify('+1 month')->format('F');

        // create tenancy Bill
        $tenancyBill = TenancyBill::create([
            'tenancy_agreement_id' => $this->id,
            'name' => $this->tenant->name.' '. $nextMonth. ' Rent Bill',
            'bill_date' => now(),
            'due_date' => // next month 5th
                date_format(
                    date_add(
                        date_create(date_format($billDate,'Y-m-d')),
                        date_interval_create_from_date_string(
                            date_format($billDate,'d') < 5 ? '0 month' : '1 month'
                        )
                    ),
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
        $billDate = new \DateTime($billDate);
        // get the various services within this property
        // generate a bill for each for this month, for this tenancy agreement
        $this->property->propertyServices()->get()->each(/**
         * @throws \Exception
         */ function ($service) use ($invoice,$billDate){
            // ensure service bill does not exist for the given month
            $serviceBillExists = TenancyBill::query()
                ->where('tenancy_agreement_id', $this->id)
                ->whereMonth('bill_date', date_format($billDate,'m'))
                ->where('service_id','=',$service->service_id)
                ->exists();

            // TODO: Extra check to prevent backdating of migrated users
            if (!$serviceBillExists && ($billDate < new \DateTime('2024-03-01'))){
                // check if the bill date is before this date (1st March, 2024)
                $serviceBillExists = true;
            }

            if (!$serviceBillExists) {// exit if the service bill exists
                // establish if property is vatable
                $isVatable = $this->property->property_type_id == 1;

                // create service bill
                TenancyBill::create([
                    'tenancy_agreement_id' => $this->id,
                    'name' => $this->tenant->name.' '.
                        date_format($billDate,'F'). ' '.
                        Services::query()->where('id','=',$service->service_id)->value('name').
                        ' Service Bill',
                    'bill_date' => now(),
                    'due_date' => // if bill date is before 5th, then 5th of the month, else 5th of the next month
                        date_format(
                            date_add(
                                date_create(date_format($billDate,'Y-m-d')),
                                date_interval_create_from_date_string(
                                    date_format($billDate,'d') < 5 ? '0 month' : '1 month'
                                )
                            ),
                            'Y-m-5'
                        ),
                    'amount' => $service->rate,
                    'vat' => $isVatable ? $service->rate * AppUtils::VAT_RATE : 0.0,
                    'total_amount' => $service->rate + ($isVatable ? $service->rate * AppUtils::VAT_RATE : 0.0),
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
