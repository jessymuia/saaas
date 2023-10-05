<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    protected $fillable = [
        'unit_id',
        'utility_id',
        'reading_date',
        'current_reading',
        'previous_reading',
        'consumption',
        'has_bill',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'archive'
    ];

    protected $casts = [
        'reading_date' => 'date'
    ];

    public function tenancyAgreement()
    {
        return $this->belongsTo(TenancyAgreement::class);
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
        try {
            DB::transaction(function () {
                // get the tenancy agreement for this meter reading
                $tenancyAgreement = TenancyAgreement::query()
                    ->where('unit_id', $this->unit_id)
                    ->whereDate('start_date', '<=', $this->reading_date)
                    ->where(function ($query) {
                        $query->whereDate('end_date', '>=', $this->reading_date)
                            ->orWhereNull('end_date');
                    })->value('id');

                if (!$tenancyAgreement) {
                    throw new \Exception('No tenancy agreement found for this meter reading');
                }

                // get the property utility for this meter reading
                $propertyUtility = PropertyUtility::query()
                    ->where('property_id', $this->unit->property_id)
                    ->where('utility_id', $this->utility_id)
                    ->where('status', '=',1)
                    ->select(['rate_per_unit','billing_type_id'])
                    ->first();

                // create invoice if not exists
                $invoice = Invoice::query()
                    ->where('tenancy_agreement_id', $tenancyAgreement)
                    ->whereMonth('issue_date', date_format($this->reading_date,'m'))
                    ->first();

                if (!$invoice) {
                    $invoice = Invoice::create([
                        'tenancy_agreement_id' => $tenancyAgreement,
                        'issue_date' => $this->reading_date,
                        'created_by' => $this->created_by,
                    ]);
                }

                // create tenancy Bill
                $tenancyBill = TenancyBill::create([
                    'tenancy_agreement_id' => $tenancyAgreement,
                    'name' => date_format($this->reading_date,'F'). ' '. $this->utility->name. ' Bill',
                    'bill_date' => $this->reading_date,
                    'due_date' => // next month 15th
                        date_format(
                            date_add(
                                date_create($this->reading_date),
                                date_interval_create_from_date_string('1 month')
                            ),
                            'Y-m-15'
                        ),
                    'amount' => $this->consumption * $propertyUtility->rate_per_unit,
                    'billing_type_id' => $propertyUtility->billing_type_id,
                    'invoice_id' => $invoice->id,
                    'utility_id' => $this->utility_id,
                    'created_by' => $this->created_by,
                ]);

                // update the value of has_bill in the meter reading
                $this->update([
                    'has_bill' => 1,
                    'updated_by' => auth()->user()->id,
                ]);
            });
        } catch (\Exception $e) {
            // Log error
            Log::error($e->getMessage());
        }
    }
}
