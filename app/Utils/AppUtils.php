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
    const REFERENCES_NAVIGATION_GROUP = 'References';
    const ACCESS_MANAGEMENT_NAVIGATION_GROUP = 'Access Management';
    const TENANCY_MANAGEMENT_NAVIGATION_GROUP = 'Tenancy Management';
    const ACCOUNTING_NAVIGATION_GROUP = 'Accounting';

    const VAT_RATE = 0.16;

    public static function defaultTableColumns(Blueprint $table) // applied to all tables except manual invoices and users
    {
        $table->id();
        $table->timestamps();
        $table->softDeletes();
        $table->tinyInteger('status')->default(1);
        $table->tinyInteger('archive')->default(0);
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();

        // foreign keys
        $table->foreign('created_by')->references('id')->on('users');
        $table->foreign('updated_by')->references('id')->on('users');
        $table->foreign('deleted_by')->references('id')->on('users');

        return $table;
    }

    public static function generateBills($isBillsForNextMonth=false)
    {
        try {
            DB::transaction(function () use ($isBillsForNextMonth){
                // get all meter readings and create
                MeterReading::query()
                    ->where('has_bill', false)
                    ->select('id','unit_id', 'utility_id', 'consumption', 'reading_date')
                    ->orderBy('reading_date', 'asc')
                    ->whereHas('tenancyAgreement', function ($query){
                        // check for active tenancy agreements
                        $query->whereDate('start_date', '<=', DB::raw('meter_readings.reading_date'))
                            ->where(function ($query) {
                                $query->whereDate('end_date', '>=', DB::raw('meter_readings.reading_date'))
                                    ->orWhereNull('end_date');
                            });
                    })
                    ->chunk(100, function ($meterReadings) {
                        if ($meterReadings->isNotEmpty()){
                            foreach ($meterReadings as $meterReading) {
                                $meterReading->createBill();
                            }
                        }
                    });
                // get all the tenancy agreements that don't have unit_occupation_monthly_logs
                // and create tenancy bills for them for rent
                // then proceed to create occupation logs for them
                $tenancyAgreements = TenancyAgreement::query()
                    ->select('id', 'unit_id', 'tenant_id', 'agreement_type_id','billing_type_id','start_date',
                        'end_date','amount','created_at','escalation_rate','next_escalation_date',
                        'escalation_period_in_months')
                    ->orderBy('start_date', 'asc')
                    ->get();

                // check that the tenancy agreement has no occupation logs for the month
                foreach ($tenancyAgreements as $tenancyAgreement) {
                    // loop through the dates from the start date to the end date
                    // check if the month has a log, if not create a bill
                    // then create a log
                    $startDate = $tenancyAgreement->start_date;

                    // added logic to avoid going back in time largely
                    $startOfLastMonth = now()->subMonth()->startOfMonth();
                    // check if the start date is before the start of last month, then set it to the start of last month
                    if ($startDate < $startOfLastMonth){
                        $startDate = $startOfLastMonth;
                    }

                    if ($isBillsForNextMonth){
                        $endDate = $tenancyAgreement->end_date > now()->addMonth()->endOfMonth() ? now()->addMonth()->endOfMonth() : $tenancyAgreement->end_date;
                        // check if end date is null, then set it to the end of the month
                        if (!$endDate){
                            $endDate = now()->addMonth()->endOfMonth();
                        }
                    }else{
                        $endDate = $tenancyAgreement->end_date > now()->endOfMonth() ? now()->endOfMonth() : $tenancyAgreement->end_date;
                        // check if end date is null, then set it to the end of the month
                        if (!$endDate){
                            $endDate = now()->endOfMonth();
                        }
                    }

                    $currentDate = $startDate;

                    // assumption: bill is generated beginning of the month except if 'isBillForNextMonth' flag is set TODO: FLAG:MIGRATION
                    while ($currentDate <= $endDate) {
                        $currentDate = date('Y-m-d', strtotime($currentDate));

                        if (!$tenancyAgreement->monthlyOccupationRecords()
                            ->whereYear('from_date',date('Y', strtotime($currentDate)))
                            ->whereMonth('from_date', date('m', strtotime($currentDate)))
                            ->exists())
                        {
                            // check to ensure no backdating of invoices
                            if ($currentDate < '2024-03-01'){
                                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
                                continue;
                            }
                            // check if there is an invoice that is not confirmed for this month
                            // if there is, then don't create a new one
                            $invoice = Invoice::query()
                                ->where('tenancy_agreement_id', $tenancyAgreement->id)
                                ->whereYear('invoice_for_month', date_format(new \DateTime($currentDate),'Y')) // TODO: FLAG:MIGRATION
                                ->whereMonth('invoice_for_month', date_format(new \DateTime($currentDate),'m')) // TODO: FLAG:MIGRATION
                                ->where('is_confirmed',0)
                                ->where('is_generated',0)
                                ->first();


                            if (!$invoice){
                                // create invoice if not exists
                                $invoice = new Invoice();
                                $invoice->tenancy_agreement_id = $tenancyAgreement->id;
                                $invoice->invoice_for_month = $currentDate;
                                $invoice->invoice_due_date = // if bill date is before 5th, then due date is 5th of this month, otherwise 5th of next month
                                    date_format(
                                        date_add(
                                            date_create($currentDate),
                                            date_interval_create_from_date_string(
                                                date_format(new \DateTime($currentDate),'d') < 5 ? '0 month' : '1 month'
                                            )
                                        ),
                                        'Y-m-5'
                                    );
                                $invoice->created_by = auth()->user()->id;

                                $invoice->save();
                            }
                            $tenancyBillId = $tenancyAgreement->createRentBill($currentDate,$invoice); //TODO MIGRATION:FLAG review this process
                            $tenancyAgreement->createServiceBill($currentDate,$invoice);
                            if ($tenancyBillId != -1){
                                $tenancyAgreement->createUnitOccupationMonthlyRecord($currentDate,$tenancyBillId);
                            }
                        }
                        // check if given property id then generate the service bills // TODO: MIGRATION:FLAG remove later
                        if ($tenancyAgreement->unit?->property_id == 1){ // check only for Avenue Mall
                            $invoice = Invoice::query()
                                ->where('tenancy_agreement_id', $tenancyAgreement->id)
                                ->whereMonth('invoice_for_month', date_format(new \DateTime($currentDate),'m')) // TODO: FLAG:MIGRATION
                                ->whereYear('invoice_for_month', date_format(new \DateTime($currentDate),'Y'))
                                ->where('is_confirmed',0)
                                ->where('is_generated',0)
                                ->first();


                            if (!$invoice){
                                // create invoice if not exists
                                $invoice = new Invoice();
                                $invoice->tenancy_agreement_id = $tenancyAgreement->id;
                                $invoice->invoice_for_month = $currentDate;
                                $invoice->invoice_due_date = // if bill date is before 5th, then due date is 5th of this month, otherwise 5th of next month
                                    date_format(
                                        date_add(
                                            date_create($currentDate),
                                            date_interval_create_from_date_string(
                                                date_format(new \DateTime($currentDate),'d') < 5 ? '0 month' : '1 month'
                                            )
                                        ),
                                        'Y-m-5'
                                    );
                                $invoice->created_by = auth()->user()->id;

                                $invoice->save();
                            }
                            $tenancyAgreement->createServiceBill($currentDate,$invoice);
                        }
                        // check if given property id then generate the service bills // TODO: MIGRATION:FLAG remove later
                        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
                    }
//                            if (!$tenancyAgreement->unitOccupationMonthlyLogs()->whereMonth('month', now()->month)->exists()) {
//                                $tenancyAgreement->createRentBill();
//                                $tenancyAgreement->createServiceBill();
//                            }
                }

                Notification::make()
                    ->title('Success')
                    ->success()
                    ->send();
            });
        }catch (\Exception $exception){
            Log::error("-----------------------------------------------------------------------");
            // log the file causing the error
            Log::error('Error generating bills');
            Log::error($exception->getFile());
            Log::error($exception->getLine() .' '. $exception->getCode());
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
            Log::error($exception->getLine());
            Log::error("-----------------------------------------------------------------------");

            Notification::make()
                ->title('Error')
                ->body('An error occurred while generating bills. '
                    . $exception->getMessage())
                ->danger()
                ->send();
        }

//                        TenancyAgreement::query()
//                            ->whereDoesntHave('unitOccupationMonthlyLogs')
//                            ->select('id', 'unit_id', 'tenant_id', 'agreement_type_id','billing_type_id','start_date', 'end_date','amount','created_at')
//                            ->orderBy('start_date', 'asc')
//                            ->chunk(100, function ($tenancyAgreements) {
//                                Log::info('Generating bills for tenancy agreements'. $tenancyAgreements->count());
//                                foreach ($tenancyAgreements as $tenancyAgreement) {
//                                    $tenancyAgreement->createRentBill();
//                                    $tenancyAgreement->createServiceBill();
//                                }
//                            });
//
//                        // get all tenancy agreements that started lasted last month and create bills
//                        TenancyAgreement::query()
//                            ->where(function($query){
//                                $query->where('end_date', '>=', now()->subMonth()->endOfMonth())
//                                    ->orWhereDate('end_date', '>=',now()->subMonth()->endOfMonth()->subDays(5))// from 25th to 31st
//                                    ->orWhereDate('end_date', null);
//                            })
//                            ->select('id', 'unit_id', 'tenant_id', 'agreement_type_id','billing_type_id','start_date', 'end_date','amount','created_at')
//                            ->orderBy('start_date', 'asc')
//                            ->chunk(100, function ($tenancyAgreements) {
//                                Log::info('Generating bills for tenancy agreements'. $tenancyAgreements->count());
//                                foreach ($tenancyAgreements as $tenancyAgreement) {
//                                    $tenancyAgreement->createRentBill();
//                                    $tenancyAgreement->createServiceBill();
//                                }
//                            });
    }
}
