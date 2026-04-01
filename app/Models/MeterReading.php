<?php

namespace App\Models;

use App\Utils\AppUtils;
use Carbon\Carbon;
use Hamcrest\Util;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Scopes\TenantScope;
use App\Traits\BelongsToTenant;

/**
 * Class MeterReading
 * @package App\Models
 * @property int $id
 * @property int $unit_id
 * @property int $utility_id
 * @property Carbon $reading_date
 * @property float $current_reading
 * @property float $previous_reading
 * @property float $consumption
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_by
 * @property int $status
 * @property int $archive
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property-read TenancyAgreement $tenancyAgreement
 * @property-read RefUtility $utility
 * @property-read Unit $unit
 */
class MeterReading extends DefaultAppModel
{
    use BelongsToTenant;
    protected $fillable = [
        'unit_id',
        'utility_id',
        'reading_date',
        'current_reading',
        'previous_reading',
        'consumption',
        'has_bill',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
        'status',
        'archive',
        'saas_client_id',
    ];

    protected $casts = [
        'reading_date' => 'date'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);

        static::created(function ($model) {
            $model->created_by = auth()->id();
            $model->saveQuietly();
        });

        static::updated(function ($model) {
            $model->updated_by = auth()->id();
            $model->saveQuietly();
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->id();
            $model->deleted_at = now();
            $model->save();
        });
    }

    public function tenancyAgreement()
    {
//        return $this->belongsTo(TenancyAgreement::class);
        return $this->hasOneThrough(
            TenancyAgreement::class,
            Unit::class,
            'id',
            'unit_id',
            'unit_id',
            'id'
        );
    }

    public function utility()
    {
        return $this->belongsTo(RefUtility::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @throws \Exception
     */
    public function createBill()
    {
        // get the tenancy agreement for this meter reading
        $tenancyAgreement = TenancyAgreement::query()
            ->where('unit_id', $this->unit_id)
            ->whereDate('start_date', '<=', $this->reading_date)
            ->where(function ($query) {
                $query->whereDate('end_date', '>=', $this->reading_date)
                    ->orWhereNull('end_date');
            })->value('id');

        if (!$tenancyAgreement) {
            throw new \Exception('No tenancy agreement found for this meter reading for unit: id'
                . $this->unit_id . ' name ' .$this->unit->name.  ' on '
                . $this->reading_date
                . ' full record: '. $this->toJson()
            );
        }

        if(Unit::onlyTrashed()->find($this->unit_id) != null){
//            throw new \Exception('Unit has been deleted for this meter reading for unit: id'
//                . $this->unit_id . ' on '
//                . $this->reading_date
//                . ' full record: '. $this->toJson()
//            );
            return -1; // TODO: FLAG:MIGRATION consider removing later
        }

        // get the property utility for this meter reading
        $propertyUtility = PropertyUtility::query()
            ->where('property_id', $this->unit->property_id)
            ->where('utility_id', $this->utility_id)
            ->where('status', '=',1)
            ->select(['rate_per_unit','billing_type_id'])
            ->first();

        // TODO: Extra check to prevent backdating of migrated users
        if ($this->reading_date < new \DateTime('2024-03-01')){
            // check if the bill date is before this date (1st Feb, 2024)
            return -1;
        }

        // create invoice if not exists
        // check if the invoice is confirmed
        // a new invoice is only created if the previous one is confirmed
        // ensure each invoice has its own separate month bills using the due_date from the tenancy bills
        // define invoice for month TODO: FLAG:MIGRATION
        $invoiceForMonthDate = $this->reading_date->startOfMonth()->addMonth();
        $invoice = Invoice::query()
            ->where('tenancy_agreement_id', $tenancyAgreement)
            ->whereYear('invoice_for_month', date_format($invoiceForMonthDate,'Y')) //
            ->whereMonth('invoice_for_month', date_format($invoiceForMonthDate,'m')) //
//            ->whereMonth('invoice_for_month', date_format($invoiceForMonthDate,'m')) // TODO: FLAG:MIGRATION
            ->where('is_confirmed', '=', 0)
            ->where('is_generated', '=', 0)
            ->get()
            ->first();

        if (!$invoice) {
            $invoice = new Invoice();
            $invoice->tenancy_agreement_id = $tenancyAgreement;
//            $invoice->invoice_for_month = $this->reading_date; // used to ensure that the invoice is created once per month TODO: FLAG:MIGRATION
            $invoice->invoice_for_month = $invoiceForMonthDate->startOfMonth(); // used to ensure that the invoice is created once per month: utility bills are pre-billed TODO: FLAG:MIGRATION
            $invoice->invoice_due_date = // next month 5th or this month 5th if reading date is before 5th
                date_format(
                    date_add(
                        date_create($this->reading_date),
                        date_interval_create_from_date_string(
                            date_format($this->reading_date,'d') < 5 ? '0 month' : '1 month'
                        )
                    ),
                    'Y-m-05'
                );
            $invoice->created_by = auth()->user()->id;

            $invoice->save();
        }

        // establish whether unit is vatable
        $isVatable = $this->unit->property->property_type_id == 1;

        // create tenancy Bill
        $tenancyBill = TenancyBill::create([
            'tenancy_agreement_id' => $tenancyAgreement,
//            'name' => TenancyAgreement::find($tenancyAgreement)->tenant->name.' '. date_format($this->reading_date,'F'). ' '. $this->utility->name. ' Bill',
            'name' => date_format($this->reading_date,'F'). ' '. $this->utility->name. ' Bill', // TODO: FLAG:MIGRATION removed unneccesary repetion of tenant name
            'bill_date' => $this->reading_date,
            'due_date' => // next month 15th
                date_format(
                    date_add(
                        date_create($this->reading_date),
                        date_interval_create_from_date_string('1 month')
                    ),
                    'Y-m-05'
                ),
            'amount' => $this->consumption * $propertyUtility->rate_per_unit,
            'vat' => $isVatable ? ($this->consumption * $propertyUtility->rate_per_unit) * AppUtils::VAT_RATE : 0.0,
            'total_amount' => ($this->consumption * $propertyUtility->rate_per_unit) + ($isVatable ? ($this->consumption * $propertyUtility->rate_per_unit) * AppUtils::VAT_RATE : 0.0),
            'billing_type_id' => $propertyUtility->billing_type_id,
            'invoice_id' => $invoice->id,
            'utility_id' => $this->utility_id,
            'created_by' => auth()->user()->id,
        ]);

        // update the value of has_bill in the meter reading
        $this->update([
            'has_bill' => 1,
            'updated_by' => auth()->user()->id,
        ]);
    }
}
