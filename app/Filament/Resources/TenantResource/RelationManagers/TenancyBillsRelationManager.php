<?php

namespace App\Filament\Resources\TenantResource\RelationManagers;

use App\Models\Invoice;
use App\Models\MeterReading;
use App\Models\TenancyAgreement;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenancyBillsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenancyBills';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('billingType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('vat')
                    ->numeric(2)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric(2)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bill_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('generate-bills')
                    ->action(function (): void{
                        // handle errors
                        try {
                            DB::transaction(function () {
                                // get all meter readings and create
                                MeterReading::query()
                                    ->where('has_bill', false)
                                    ->select('id','unit_id', 'utility_id', 'consumption', 'reading_date')
                                    ->orderBy('reading_date', 'asc')
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
                                    ->select('id', 'unit_id', 'tenant_id', 'agreement_type_id','billing_type_id','start_date', 'end_date','amount','created_at')
                                    ->orderBy('start_date', 'asc')
                                    ->get();

                                // check that the tenancy agreement has no occupation logs for the month
                                foreach ($tenancyAgreements as $tenancyAgreement) {
                                    // loop through the dates from the start date to the end date
                                    // check if the month has a log, if not create a bill
                                    // then create a log
                                    $startDate = $tenancyAgreement->start_date;
                                    $endDate = $tenancyAgreement->end_date > now()->endOfMonth() ? now()->endOfMonth() : $tenancyAgreement->end_date;
                                    // check if end date is null, then set it to the end of the month
                                    if (!$endDate){
                                        $endDate = now()->endOfMonth();
                                    }
                                    $currentDate = $startDate;

                                    // assumption: bill is generated beginning of the month TODO: FLAG:MIGRATION
                                    while ($currentDate <= $endDate) {
                                        $currentDate = date('Y-m-d', strtotime($currentDate));
                                        if (!$tenancyAgreement->monthlyOccupationRecords()->whereMonth('from_date', date('m', strtotime($currentDate)))->exists()) {
                                            // check to ensure no backdating of invoices
                                            if ($currentDate < '2024-03-01'){
                                                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
                                                continue;
                                            }

                                            // check if there is an invoice that is not confirmed for this month
                                            // if there is, then don't create a new one
                                            $invoice = Invoice::query()
                                                ->where('tenancy_agreement_id', $tenancyAgreement->id)
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
                                        $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
                                    }
//                            if (!$tenancyAgreement->unitOccupationMonthlyLogs()->whereMonth('month', now()->month)->exists()) {
//                                $tenancyAgreement->createRentBill();
//                                $tenancyAgreement->createServiceBill();
//                            }
                                }

                                Notification::make('generate-bills-notification')
                                    ->title('Success')
                                    ->success()
                                    ->send();
                            });
                        }catch (\Exception $exception){
                            Log::error("-----------------------------------------------------------------------");
                            Log::error($exception->getMessage());
                            Log::error($exception->getTraceAsString());
                            Log::error($exception->getLine());
                            Log::error("-----------------------------------------------------------------------");

                            Notification::make('generate-bills-notification')
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
                    }),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(fn () => 'Are you sure you want to delete this tenancy bill?')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(fn ($records) => 'Are you sure you want to delete these records?'),
                ]),
            ]);
    }
}
