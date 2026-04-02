<?php

namespace App\Utils;

use App\Models\Invoice;
use App\Models\MeterReading;
use App\Models\TenancyAgreement;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppUtils
{
    const REFERENCES_NAVIGATION_GROUP          = 'References';
    const ACCESS_MANAGEMENT_NAVIGATION_GROUP   = 'Access Management';
    const TENANCY_MANAGEMENT_NAVIGATION_GROUP  = 'Tenancy Management';
    const ACCOUNTING_NAVIGATION_GROUP          = 'Accounting';

    const VAT_RATE = 0.16;

    /**
     * Add default columns to a table schema.
     *
     * @param Blueprint $table
     * @param bool $addId         
     *                           
     * @param bool $addAuditFk    
     *                            
     * @return Blueprint
     */
    public static function defaultTableColumns(
        Blueprint $table,
        bool $addId = true,
        bool $addAuditFk = true
    ): Blueprint {
        if ($addId) {
            
            $table->id();
        }

        $table->timestamps();
        $table->softDeletes();

        $table->tinyInteger('status')->default(1);
        $table->tinyInteger('archive')->default(0);

        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();


        if ($addAuditFk && $addId) {
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->cascadeOnDelete();
        }

        return $table;
    }

    public static function generateBills($isBillsForNextMonth = false)
    {
        try {
            DB::transaction(function () use ($isBillsForNextMonth) {
                // get all meter readings and create
                MeterReading::query()
                    ->where('has_bill', false)
                    ->select('id', 'unit_id', 'utility_id', 'consumption', 'reading_date')
                    ->orderBy('reading_date', 'asc')
                    ->whereHas('tenancyAgreement', function ($query) {
                        // check for active tenancy agreements
                        $query->whereDate('start_date', '<=', DB::raw('meter_readings.reading_date'))
                            ->where(function ($query) {
                                $query->whereDate('end_date', '>=', DB::raw('meter_readings.reading_date'))
                                    ->orWhereNull('end_date');
                            });
                    })
                    ->chunk(100, function ($meterReadings) {
                        if ($meterReadings->isNotEmpty()) {
                            foreach ($meterReadings as $meterReading) {
                                $meterReading->createBill();
                            }
                        }
                    });

                $tenancyAgreements = TenancyAgreement::query()
                    ->select(
                        'id', 'unit_id', 'tenant_id', 'agreement_type_id', 'billing_type_id',
                        'start_date', 'end_date', 'amount', 'created_at', 'escalation_rate',
                        'next_escalation_date', 'escalation_period_in_months'
                    )
                    ->orderBy('start_date', 'asc')
                    ->get();

                foreach ($tenancyAgreements as $tenancyAgreement) {
                    $startDate = $tenancyAgreement->start_date;

                    $startOfLastMonth = now()->subMonth()->startOfMonth();
                    if ($startDate < $startOfLastMonth) {
                        $startDate = $startOfLastMonth;
                    }

                    if ($isBillsForNextMonth) {
                        $endDate = $tenancyAgreement->end_date > now()->addMonth()->endOfMonth()
                            ? now()->addMonth()->endOfMonth()
                            : $tenancyAgreement->end_date;
                        if (!$endDate) {
                            $endDate = now()->addMonth()->endOfMonth();
                        }
                    } else {
                        $endDate = $tenancyAgreement->end_date > now()->endOfMonth()
                            ? now()->endOfMonth()
                            : $tenancyAgreement->end_date;
                        if (!$endDate) {
                            $endDate = now()->endOfMonth();
                        }
                    }

                    $currentDate = $startDate;

                    while ($currentDate <= $endDate) {
                        $currentDate = date('Y-m-d', strtotime($currentDate));

                        if (!$tenancyAgreement->monthlyOccupationRecords()
                            ->whereYear('from_date', date('Y', strtotime($currentDate)))
                            ->whereMonth('from_date', date('m', strtotime($currentDate)))
                            ->exists()) {
                            if ($currentDate < '2024-03-01') {
                                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
                                continue;
                            }

                            $invoice = Invoice::query()
                                ->where('tenancy_agreement_id', $tenancyAgreement->id)
                                ->whereYear('invoice_for_month', date_format(new \DateTime($currentDate), 'Y'))
                                ->whereMonth('invoice_for_month', date_format(new \DateTime($currentDate), 'm'))
                                ->where('is_confirmed', 0)
                                ->where('is_generated', 0)
                                ->first();

                            if (!$invoice) {
                                $invoice = new Invoice();
                                $invoice->tenancy_agreement_id = $tenancyAgreement->id;
                                $invoice->invoice_for_month = $currentDate;
                                $invoice->invoice_due_date =
                                    date_format(
                                        date_add(
                                            date_create($currentDate),
                                            date_interval_create_from_date_string(
                                                date_format(new \DateTime($currentDate), 'd') < 5
                                                    ? '0 month'
                                                    : '1 month'
                                            )
                                        ),
                                        'Y-m-5'
                                    );
                                $invoice->created_by = auth()->user()->id;
                                $invoice->save();
                            }

                            $tenancyBillId = $tenancyAgreement->createRentBill($currentDate, $invoice);
                            $tenancyAgreement->createServiceBill($currentDate, $invoice);

                            if ($tenancyBillId != -1) {
                                $tenancyAgreement->createUnitOccupationMonthlyRecord($currentDate, $tenancyBillId);
                            }
                        }

                        if ($tenancyAgreement->unit?->property_id == 1) {
                            $invoice = Invoice::query()
                                ->where('tenancy_agreement_id', $tenancyAgreement->id)
                                ->whereMonth('invoice_for_month', date_format(new \DateTime($currentDate), 'm'))
                                ->whereYear('invoice_for_month', date_format(new \DateTime($currentDate), 'Y'))
                                ->where('is_confirmed', 0)
                                ->where('is_generated', 0)
                                ->first();

                            if (!$invoice) {
                                $invoice = new Invoice();
                                $invoice->tenancy_agreement_id = $tenancyAgreement->id;
                                $invoice->invoice_for_month = $currentDate;
                                $invoice->invoice_due_date =
                                    date_format(
                                        date_add(
                                            date_create($currentDate),
                                            date_interval_create_from_date_string(
                                                date_format(new \DateTime($currentDate), 'd') < 5
                                                    ? '0 month'
                                                    : '1 month'
                                            )
                                        ),
                                        'Y-m-5'
                                    );
                                $invoice->created_by = auth()->user()->id;
                                $invoice->save();
                            }

                            $tenancyAgreement->createServiceBill($currentDate, $invoice);
                        }

                        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
                    }
                }

                Notification::make()->title('Success')->success()->send();

            });
        } catch (\Exception $exception) {
            Log::error("-----------------------------------------------------------------------");
            Log::error('Error generating bills');
            Log::error($exception->getFile());
            Log::error($exception->getLine() . ' ' . $exception->getCode());
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
            Log::error($exception->getLine());
            Log::error("-----------------------------------------------------------------------");

            Notification::make()
                ->title('Error')
                ->body('An error occurred while generating bills. ' . $exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
