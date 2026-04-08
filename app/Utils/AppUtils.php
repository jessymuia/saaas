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
use Illuminate\Support\Facades\Schema;

class AppUtils
{
    const REFERENCES_NAVIGATION_GROUP          = 'References';
    const ACCESS_MANAGEMENT_NAVIGATION_GROUP   = 'Access Management';
    const TENANCY_MANAGEMENT_NAVIGATION_GROUP  = 'Tenancy Management';
    const ACCOUNTING_NAVIGATION_GROUP          = 'Accounting';

    const VAT_RATE = 0.16;

    /**
     * Add default columns to a table schema (UUID edition).
     *
     * Adds: uuid id (gen_random_uuid()), version, timestampsTz, softDeletes,
     * status (bool), archive (bool), and optionally audit FK columns.
     *
     * @param Blueprint $table
     * @param bool $addId        Whether to add the UUID primary key column
     * @param bool $addAuditFk   Whether to add created_by/updated_by/deleted_by with FK constraints
     * @return Blueprint
     */
    public static function defaultTableColumns(
        Blueprint $table,
        bool $addId = true,
        bool $addAuditFk = true
    ): Blueprint {
        if ($addId) {
            if (!Schema::hasColumn($table->getTable(), 'id')) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            }
        }

        $table->decimal('version', 10, 2)->default(1.0);
        $table->timestampsTz();
        $table->softDeletes();

        $table->boolean('status')->default(true);
        $table->boolean('archive')->default(false);

        if ($addAuditFk) {
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        } else {
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
        }

        return $table;
    }

    public static function generateBills($isBillsForNextMonth = false)
    {
        try {
            DB::transaction(function () use ($isBillsForNextMonth) {
                MeterReading::query()
                    ->where('has_bill', false)
                    ->select('id', 'unit_id', 'utility_id', 'consumption', 'reading_date')
                    ->orderBy('reading_date', 'asc')
                    ->whereHas('tenancyAgreement', function ($query) {
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
                                $invoice->created_by = auth()->user()?->id;
                                $invoice->save();
                            }

                            $tenancyBillId = $tenancyAgreement->createRentBill($currentDate, $invoice);
                            $tenancyAgreement->createServiceBill($currentDate, $invoice);

                            if ($tenancyBillId != -1) {
                                $tenancyAgreement->createUnitOccupationMonthlyRecord($currentDate, $tenancyBillId);
                            }
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
            Log::error("-----------------------------------------------------------------------");

            Notification::make()
                ->title('Error')
                ->body('An error occurred while generating bills. ' . $exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
