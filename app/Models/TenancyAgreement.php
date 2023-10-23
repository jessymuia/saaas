<?php

namespace App\Models;

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

    public function rentPayments()
    {
        return $this->hasMany(RentPayment::class);
    }

    /*
     * Create the rental bill for the tenancy agreement
     */
    public function createRentBill(){
        try {
            DB::transaction(function (){
                // get date of last invoice linked to this tenancy agreement
                $lastInvoiceDate = Invoice::query()
                    ->select('created_at')
                    ->where('tenancy_agreement_id', $this->id)
                    // where there is no tenancy bill linked to rent payment
                    ->whereNotExists(function ($query){
                        $query->select(DB::raw(1))
                            ->from('tenancy_bills')
                            ->whereRaw('tenancy_bills.invoice_id = invoices.id')
                            ->where('tenancy_bills.service_id',null)
                            ->where('tenancy_bills.utility_id',null);
                    })
                    ->orderBy('created_at','desc')
                    ->value('created_at');

                if (!$lastInvoiceDate){ // the invoice already exists
                    return;
                }

                // create invoice if not exists
                $invoice = Invoice::query()
                    ->where('tenancy_agreement_id', $this->id)
                    ->whereMonth('created_at', date_format($lastInvoiceDate,'m'))
                    ->get()
                    ->first();

                if (!$invoice) {
                    $invoice = new Invoice();
                    $invoice->tenancy_agreement_id = $this->id;
//                    $invoice->issue_date = $this->reading_date;
                    $invoice->created_by = auth()->user()->id;

                    $invoice->save();
                }

                // ensure there is no bill for this month
                $tenancyBillExists = TenancyBill::query()
                    ->where('tenancy_agreement_id', $this->id)
                    ->whereMonth('bill_date', date_format($lastInvoiceDate,'m'))
                    ->where('service_id',null)
                    ->where('utility_id',null)
                    ->exists();

                if ($tenancyBillExists){ // exit if the rent bill exists
                    return;
                }

                // create tenancy Bill
                TenancyBill::create([
                    'tenancy_agreement_id' => $this->id,
                    'name' => $this->tenant->name.' '. date_format($lastInvoiceDate,'F'). ' Rent Bill',
                    'bill_date' => now(),
                    'due_date' => // next month 5th
                        date_format(
                            date_add(
                                date_create($lastInvoiceDate),
                                date_interval_create_from_date_string('1 month')
                            ),
                            'Y-m-5'
                        ),
                    'amount' => $this->amount,
                    'billing_type_id' => $this->billing_type_id,
                    'invoice_id' => $invoice->id,
                    'created_by' => auth()->user()->id,
                ]);
            });
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
            Log::error("------------------------------------------------------------------------------");
        }
    }

    public function createServiceBill(){
        try {
            DB::transaction(function (){
                // get date of last invoice linked to this tenancy agreement
                $lastInvoiceDate = Invoice::query()
                    ->select('created_at')
                    ->where('tenancy_agreement_id', $this->id)
                    ->orderBy('created_at','desc')
                    ->value('created_at');

                // get date of last meter reading linked to this tenancy agreement
                $lastMeterReadingDate = MeterReading::query()
                    ->select('reading_date')
                    ->where('unit_id', $this->unit_id)
                    ->orderBy('reading_date','desc')
                    ->value('reading_date');

                // get the latest date
                $latestProcessingDate = max($lastInvoiceDate, $lastMeterReadingDate);

                // create invoice if not exists
                $invoice = Invoice::query()
                    ->where('tenancy_agreement_id', $this->id)
                    ->whereMonth('created_at', date_format($latestProcessingDate,'m'))
                    ->get()
                    ->first();

                if (!$invoice) {
                    $invoice = new Invoice();
                    $invoice->tenancy_agreement_id = $this->id;
//                    $invoice->issue_date = $this->reading_date;
                    $invoice->created_by = auth()->user()->id;

                    $invoice->save();
                }

                // check if the service bill exists
                $serviceBillExists = TenancyBill::query()
                    ->where('tenancy_agreement_id', $this->id)
                    ->whereMonth('bill_date', date_format($lastMeterReadingDate,'m'))
                    ->where('service_id','!=',null)
                    ->exists();

                if ($serviceBillExists){ // exit if the service bill exists
                    return;
                }

                // get the various services within this property
                // generate a bill for each for this month, for this tenancy agreement
                $this->property->propertyServices()->get()->each(function ($service) use ($invoice,$lastInvoiceDate){
                    // create service bill
                    TenancyBill::create([
                        'tenancy_agreement_id' => $this->id,
                        'name' => $this->tenant->name.' '.
                            date_format($lastInvoiceDate,'F').
                            Services::where('id',$service->id)->value('name').
                            ' Service Bill',
                        'bill_date' => now(),
                        'due_date' => // next month 5th
                            date_format(
                                date_add(
                                    date_create($lastInvoiceDate),
                                    date_interval_create_from_date_string('1 month')
                                ),
                                'Y-m-5'
                            ),
                        'amount' => $service->rate,
                        'billing_type_id' => $service->billing_type_id,
                        'service_id' => $service->service_id,
                        'invoice_id' => $invoice->id,
                        'created_by' => auth()->user()->id
                    ]);
                });
            });
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
            Log::error("------------------------------------------------------------------------------");
        }
    }
}
