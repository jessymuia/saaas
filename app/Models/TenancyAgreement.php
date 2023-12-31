<?php

namespace App\Models;

use App\Utils\AppUtils;
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
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
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
        return $this->hasMany(UnitOccupationMonthlyRecords::class);
    }

    /**
     * Tenancy agreement files relationship
     */
    public function tenancyAgreementFiles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TenancyAgreementFiles::class);
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
            ->whereMonth('bill_date', date_format($billDate,'m'))
            ->where('service_id',null)
            ->where('utility_id',null)
            ->first();

        if ($tenancyBillExists){ // exit if the rent bill exists
            return $tenancyBillExists->id;
        }

        // establish if unit is vatable
        $isVatable = $this->unit->property->property_type_id == 1;

        // create tenancy Bill
        $tenancyBill = TenancyBill::create([
            'tenancy_agreement_id' => $this->id,
            'name' => $this->tenant->name.' '. date_format($billDate,'F'). ' Rent Bill',
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
        $unitOccupationMonthlyRecord->end_date = $this->end_date < date_format($billDate,'Y-m-t')
            ? $this->end_date
            : date_format($billDate,'Y-m-t');
        $unitOccupationMonthlyRecord->tenancy_bill_id = $tenancyBillId;
        $unitOccupationMonthlyRecord->created_by = auth()->user()->id;

        $unitOccupationMonthlyRecord->save();
    }
}
