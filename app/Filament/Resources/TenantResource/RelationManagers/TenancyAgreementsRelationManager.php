<?php

namespace App\Filament\Resources\TenantResource\RelationManagers;

use App\Filament\Exports\TenancyAgreementExporter;
use App\Models\CompanyDetails;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\ManualInvoices;
use App\Models\TenancyAgreement;
use App\Rules\CheckOccupancyOfUnit;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TenancyAgreementsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenancyAgreements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->rules([
                        fn(Get $get) : CheckOccupancyOfUnit => new CheckOccupancyOfUnit($get('start_date'),$form->getOperation()),
                        'required'
                    ])
                    ->required()
                    ->relationship('unit', 'name'),
                Forms\Components\Select::make('billing_type_id')
                    ->label('Billing Type')
                    ->required()
                    ->relationship('billingType', 'type'),
                Forms\Components\Select::make('agreement_type_id')
                    ->label('Agreement Type')
                    ->required()
                    ->relationship('agreementType', 'type'),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->nullable()
                    ->after('start_date'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('deposit_amount')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('balance_carried_forward')
                    ->nullable()
                    ->numeric(),
                Forms\Components\Checkbox::make('is_escalation')
                    ->label('Define Escalation')
                    ->reactive(),
                Forms\Components\TextInput::make('escalation_rate')
                    ->label('Escalation Rate')
                    ->numeric()
                    ->maxValue(100)
                    ->visible(function (Get $get){
                        return $get('is_escalation') == true;
                    })
                    ->requiredIf('is_escalation',true)
                    ->reactive(),
                Forms\Components\TextInput::make('escalation_period_in_months')
                    ->label('Escalation Period(months)')
                    ->numeric()
                    ->reactive()
                    ->visible(function (Get $get){
                        return $get('is_escalation') == true;
                    })
//                    ->disabledOn('edit') // TODO: FLAG:MIGRATION
                    ->afterStateUpdated(function (Get $get,Forms\Set $set){
                        // get start date
//                        $get('start_date');
                        // convert start date to carbon date data type
                        $start_date = Carbon::createFromFormat('Y-m-d',$get('start_date'));
                        $next_date = $start_date->addMonths($get('escalation_period_in_months'));
                        while ($next_date < today()){
                            $next_date = $next_date
                                ->addMonths($get('escalation_period_in_months'));
                        }
                        $set('next_escalation_date',$next_date->format('Y-m-d'));
                        unset($start_date);
                        unset($next_date);
                    })
                    ->requiredIf('is_escalation',true),
                Forms\Components\DatePicker::make('next_escalation_date')
                    ->visible(function (Get $get) use ($form) {
                        return ($get('is_escalation') == true
//                            && $form->getOperation() == 'create' TODO: FLAG:MIGRATION
                        );
                    })
                    ->reactive()
//                    ->readOnly() // TODO: FLAG:MIGRATION
//                Forms\Components\Checkbox::make('is_migrating')
//                    ->label('Are you migrating from another system?')
//                    ->reactive(),
//                Forms\Components\TextInput::make('balance_carried_forward')
//                    ->nullable()
//                    ->numeric()
//                    ->minValue(1)
//                    ->reactive()
//                    ->visible(function (Get $get){
//                        return $get('is_migrating') == true;
//                    })
//                    ->requiredIf('is_migrating',true),
                // allow for addition of tenancy agreement files using the file uploader and relationship
//                Forms\Components\FileUpload::make('tenancyAgreementFiles')
//                    ->label('Tenancy Agreement Files')
//                    ->acceptedFileTypes(['application/pdf'])
//                    ->maxSize(1024 * 1024 * 5)
//                    ->disk('local')
//                    ->directory('tenancy_agreement_files')
//                    ->visibility('private'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('billingType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agreementType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('next_escalation_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('escalation_period_in_months')
                    ->label('Escalation Period (months)')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('escalation_rate')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance_carried_forward')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('deposit_amount')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_by')
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
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['created_by'] = auth()->user()->id;
                        return $data;
                    }),
                ExportAction::make()
                    ->exporter(TenancyAgreementExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['updated_by'] = auth()->user()->id;
                        return $data;
                    }),
                Tables\Actions\Action::make('generate-statement-of-account')
                    ->label('Generate Statement of Account')
                    ->action(fn(TenancyAgreement $record)=>$this->generateStatementOfAccountVersionTwo($record)),
//                    ->action(fn(TenancyAgreement $record)=>$this->generateStatementOfAccount($record)),
//                    ->action(fn()=>$this->generateStatementOfAccount($this->ownerRecord)),
                Tables\Actions\Action::make('generate-invoice-for-balance-carried-forward')
                    ->label('Bill Balance Carried Forward')
                    ->icon("heroicon-m-document-check")
                    ->visible(fn($record) => !$record->has_invoice_for_balance_carried_forward)
                    ->requiresConfirmation(fn($record) => 'Are you sure you would like to create an invoice for the balance carried forward for this tenancy agreement?')
                    ->action(function (TenancyAgreement $record) {
                        $response = $record->createInvoiceForBalanceCarriedForward();

                        if ($response["status"] == -1){
                            // pop up a toast message
                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body($response["message"])
                                ->duration(5000)
                                ->icon('heroicon-o-x-circle')
                                ->send();
                        }
                        if ($response["status"] == 1){
                            // pop up a toast message
                            Notification::make()
                                ->title('Success')
                                ->success()
                                ->body($response["message"])
                                ->duration(5000)
                                ->icon('heroicon-o-check-circle')
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('generate-lease-schedule')
                    ->label('Generate Lease Schedule')
                    ->icon('heroicon-o-document-text')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Lease Schedule Report')
                    ->modalDescription('Please confirm generating the lease schedule report.')
                    ->form([
                        Forms\Components\DatePicker::make('custom_end_date')
                            ->label('End Date')
                            ->visible(fn ($record) => is_null($record->end_date))
                            ->required(fn ($record) => is_null($record->end_date))
                            ->after('start_date'),
                    ])
                    ->action(function (TenancyAgreement $record, array $data) {
                        return $record->generateLeaseSchedule(
                            isset($data['custom_end_date']) ? $data['custom_end_date'] : null
                        );
                    }),
                Tables\Actions\DeleteAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['deleted_by'] = auth()->user()->id;
                        return $data;
                    })
                    ->requiresConfirmation(fn ($record) => 'Are you sure you want to delete this tenancy agreement?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(TenancyAgreementExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }

    private function generateStatementOfAccount($tenancyAgreement)
    {
        // get all invoices and convert to array
        $invoices = Invoice::query()
            ->where('tenancy_agreement_id', '=', $tenancyAgreement->id)
            ->orderBy('created_at', 'desc')
            ->select(['id', 'invoice_for_month as transaction_date','invoice_due_date'])
            ->selectRaw("concat('INV #', id,'. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, concat('invoice') as transaction_type")
//            ->with('creditNote', function ($query){
//                $query->select('id');
//            })
            ->get(['amount','unpaid_amount'])
            ->toArray();

        $manualInvoices = ManualInvoices::query()
            ->where('tenant_id', '=', $tenancyAgreement->tenant_id)
            ->orderBy('created_at', 'desc')
            ->select(['id', 'invoice_for_month as transaction_date','invoice_due_date'])
            ->selectRaw("concat('INV #', id,'. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, concat('invoice') as transaction_type")
            ->get(['amount','unpaid_amount'])
            ->toArray();

        // combine the two arrays, and sort them by transaction date
        $invoices = array_merge($invoices, $manualInvoices);
        usort($invoices, function ($a, $b) {
            return $a['transaction_date'] <=> $b['transaction_date'];
        });

//        dd($invoices);
        // get all credit notes and convert to array
        $creditNotes = CreditNote::query()
            ->orderBy('created_at', 'desc')
            ->whereHas('invoice', function ($query) use ($tenancyAgreement) {
                $query->where('tenancy_agreement_id', '=', $tenancyAgreement->id);
            })
            ->select(['id', 'created_at as transaction_date','amount_credited as amount'])
            ->selectRaw("concat('CRN #', id,'. ', name,'. Issued on ') as transaction, concat('credit_note') as transaction_type")
            ->get()
            ->toArray();
//        dd($creditNotes);
        // get all invoice payments
        $invoicePayments = InvoicePayment::query()
            ->orderBy('payment_date', 'desc')
            ->where(function ($query) use ($tenancyAgreement) {
                $query->where('tenant_id', '=', $tenancyAgreement->tenant_id);
            })
            ->select(['id', 'payment_date as transaction_date','amount'])
            ->selectRaw("concat('PMT #', id,'. Paid on ') as transaction, concat('payment') as transaction_type")
            ->get()
            ->toArray();
//        dd($invoicePayments);

        // merge the three arrays
        $transactions = array_merge($invoices, $creditNotes, $invoicePayments);
        // sort the array by transaction date
        usort($transactions, function ($a, $b) {
            return $a['transaction_date'] <=> $b['transaction_date'];
        });

        // obtain the total due
        $amountDue = 0;
        foreach ($transactions as $transaction) {
            if ($transaction['transaction_type'] == 'invoice') {
                $amountDue += $transaction['amount'];
            } elseif ($transaction['transaction_type'] == 'credit_note') {
                $amountDue -= $transaction['amount'];
            } elseif ($transaction['transaction_type'] == 'payment') {
                $amountDue -= $transaction['amount'];
            }
        }

        $tenant = $tenancyAgreement->tenant()->first();

//        dd($totalDue);
        try {
            $statementOfAccountItems = '
                <tr style="height: 30px;">
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="2" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::createFromFormat('Y-m-d',$tenancyAgreement->start_date)->format('M j, Y').'</td>
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="3" style="text-align: left; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000;  color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">Balance Forward</td>
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;"></td>
                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format(0,2).'</td>
                </tr>';

            $propertyName = $tenancyAgreement->property->name;

            $unitName = $tenancyAgreement->unit->name;
            $balanceCarriedForward = 0;
            $runningBalance = $balanceCarriedForward;
            $runningAmountDue = 0;

//            foreach ($transactions as $transaction) {
//                $signOfTransaction = $transaction['transaction_type'] == 'invoice' ? '+' : '-';
//                $runningBalance += $transaction['transaction_type'] == 'invoice' ? $amountDue - $transaction['amount'] : $amountDue + $transaction['amount'];
//                $statementOfAccountItems .= '
//                    <tr style="height: 30px;">
//                        <td class="s8b" dir="ltr" colspan="1" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::createFromFormat('Y-m-d H:i:s',$transaction['transaction_date'])->format('F j, Y').'</td>
//                        <td class="s8" dir="ltr" colspan="4" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$transaction['transaction'].'</td>
//                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: right; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$signOfTransaction.number_format($transaction['amount'],2).'</td>
//                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: right; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format($runningBalance,2).'</td>
//                    </tr>';
//            }
            foreach ($transactions as $transaction) {
                $signOfTransaction = $transaction['transaction_type'] == 'invoice' ? '' : '-';
                $runningBalance += $transaction['transaction_type'] == 'invoice' ? $runningAmountDue + $transaction['amount'] : $runningAmountDue - $transaction['amount'];
                $statementOfAccountItems .= '
                    <tr style="height: 30px;">
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="2" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::parse($transaction['transaction_date'])->format('M j, Y').'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="3" style="text-align: left; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000;  color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$transaction['transaction'].'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$signOfTransaction.number_format($transaction['amount'],2).'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format($runningBalance,2).'</td>
                    </tr>';
            }

            // check if the statement of account items can fit in one page,
            // if they are more than one page, then add a page break
            // if less than one page, padd the table with empty rows
            $statementOfAccountItems .= str_repeat(
                '
                        <tr style="height: 30px;">
                            <td class="s_cell_with_right_left_border" colspan="2"></td>
                            <td class="s_cell_with_right_left_border" colspan="3"></td>
                            <td class="s_cell_with_right_left_border" colspan="1"></td>
                            <td class="s_cell_with_right_left_border" colspan="1"></td>
                        </tr>',
                19 - count($transactions)
            );

            $current = 0;
            $oneToThirtyPastDue = 0;
            $thirtyOneToSixtyPastDue = 0;
            $sixtyOneToNinetyPastDue = 0;
            $overNinetyPastDue = 0;
            // iterate over the invoices obtaining the current, 1-30, 31-60, 61-90, over 90
            foreach ($invoices as $invoice) {
                $invoiceDueDate = Carbon::createFromFormat('Y-m-d',$invoice['invoice_due_date']);
                $daysDifference = $invoiceDueDate->diffInDays(Carbon::now());
                if ($daysDifference > 90){
                    $overNinetyPastDue += $invoice['amount'];
                } elseif ($daysDifference > 60){
                    $sixtyOneToNinetyPastDue += $invoice['amount'];
                } elseif ($daysDifference > 30){
                    $thirtyOneToSixtyPastDue += $invoice['amount'];
                } elseif ($daysDifference > 0 ){
                    $oneToThirtyPastDue += $invoice['amount'];
                } elseif ($daysDifference == 0){
                    $current += $invoice['amount'];
                }
            }

            $detailsArray = [
                'customerName' => $unitName.' '.$tenancyAgreement->tenant->name,
                'propertyName' => $propertyName,
                'dateGenerated'=> Carbon::now()->format('M j, Y'),
                'logoUrl'=>'file://'.getcwd().'/images/hamud_top_doc_logo.png',
                'amountDue' => number_format($amountDue,2),
                'amountEnc' => number_format(0,2),
                'statementOfAccountItemsHTML' => $statementOfAccountItems,
                'current'=> number_format($current,2),
                'oneToThirtyPastDue'=> number_format($oneToThirtyPastDue,2),
                'thirtyOneToSixtyPastDue'=> number_format($thirtyOneToSixtyPastDue,2),
                'sixtyOneToNinetyPastDue'=> number_format($sixtyOneToNinetyPastDue,2),
                'overNinetyPastDue'=> number_format($overNinetyPastDue,2),
            ];

            $content = File::get(resource_path('documents/templates/statement-of-account-output-document.html'));

            foreach ($detailsArray as $key => $value) {
                $content = str_replace("@#$key", $value, $content);
            }

            $pdfName = "Statement of Account".
                '_for_' .
                $tenancyAgreement->tenant->name . '_' .
                $propertyName .
                '_for_unit_' .
                $unitName .
                '_'.$tenancyAgreement->id;

            // get the path but without the clatter file system
            $pdfPath = Storage::path('statements_of_account') . '/' . $pdfName . '.pdf';

//            Storage::url($pdfPath);

//            Storage::put($pdfPath, $content);
            $snappy = App::make('snappy.pdf');
            $snappy->setOption('enable-local-file-access', true);
            $snappy->setOption('disable-smart-shrinking', false);
            $snappy->setOption('margin-bottom', '1in');
            $snappy->setOption('margin-left', '1in');
            $snappy->setOption('margin-right', '1in');
            $snappy->setOption('margin-top', '1in');
//            $snappy->generateFromHtml($content, Storage::url($pdfPath));
//            Pdf::generateFromHtml($content, Storage::url($pdfPath));
            $snappy->generateFromHtml($content, $pdfPath);

//            \Barryvdh\DomPDF\PDF::loadView('documents.templates.invoice-output-document', $detailsArray)->save($pdfPath);
//            \Barryvdh\DomPDF\Facade\Pdf::loadView('documents.templates.invoice-output-document', $detailsArray)->save($pdfPath);
//            $pdf = SnappyPdf::loadView('documents.templates.invoice-output-document', $detailsArray)->output();
//            Log::error("Ndio hii: " . $pdf);
//            $pdf = SnappyPdf::loadView('documents.templates.invoice-output-document', $detailsArray);
//            $pdf = SnappyPdf::loadView('documents.templates.invoice-output-document', $detailsArray)->save($pdfPath);
//            $pdf = SnappyPdf::loadHTML($content)->output();
            Log::error("Ndio hii: " . $pdfPath);
//            Storage::put($pdfPath, $pdf->output());

//            Log::info(Storage::url($pdfPath));

            Log::info('--------------------------------------------------------------------------');

            // trigger download of the file then delete it
            return response()->download($pdfPath)->deleteFileAfterSend(true);

            // check if file exists
//            if (file_exists($pdfPath)) {
//                $savedPath = explode('/', $pdfPath);
//                // retrieve the string after the string 'app'
//                foreach ($savedPath as $key => $value) {
//                    if ($value == 'app') {
//                        $savedPath = implode("/", array_slice($savedPath, $key + 1));
//                        break;
//                    }
//                }
////                $this->is_generated = 1;
////                $this->document_url = $savedPath;
////                $this->updated_by = auth()->user()->id;
////
////                $this->save();
//
//                return true;
//            } else {
//                return false;
//            }
        }catch (\Exception $exception){
            Log::error($exception->getTraceAsString());
            Log::error($exception->getMessage());
            return false;
        }
    }

    public function generateStatementOfAccountVersionTwo($tenancyAgreement){
        // get all the invoices for the tenancy agreement, with the credit notes, debit notes and payments for the given invoices
        $invoices = Invoice::query()
            ->where('tenancy_agreement_id', '=', $tenancyAgreement->id)
            ->orderBy('created_at', 'desc')
            ->select(['id as invoice_id', 'invoice_for_month as transaction_date','invoice_due_date'])
            ->selectRaw("concat('INV #', id,'. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, concat('invoice') as transaction_type")
            ->where('is_confirmed', '=', true)
            ->get(['amount','unpaid_amount'])
            ->toArray();

        $manualInvoices = ManualInvoices::query()
            ->where('tenant_id', '=', $tenancyAgreement->tenant_id)
            ->orderBy('created_at', 'desc')
            ->select(['id as invoice_id', 'invoice_for_month as transaction_date','invoice_due_date'])
            ->selectRaw("concat('INV #', id,'. Due on ', TO_CHAR(invoice_for_month,'Mon DD, YYYY')) as transaction, concat('invoice') as transaction_type")
            ->where('is_confirmed','=', true)
            ->get(['amount','unpaid_amount'])
            ->toArray();

        // combine the two arrays, and sort them by transaction date
        $invoices = array_merge($invoices, $manualInvoices);
        // obtain unpaid amount for each invoice
        foreach ($invoices as $key => $invoice){
            // check for null results
            if(Invoice::query()->find($invoice['invoice_id']) == null)
                Log::error("This one is an error, invoice id: ".$invoice['invoice_id']);
            $invoice['unpaid_amount'] = Invoice::query()->find($invoice['invoice_id'])->unpaid_amount ?? ManualInvoices::query()->find($invoice['invoice_id'])->unpaid_amount;
            $invoice['amount'] = Invoice::query()->find($invoice['invoice_id'])->amount ?? ManualInvoices::query()->find($invoice['invoice_id'])->amount;
            $invoices[$key] = $invoice;
        }

//        dd($invoices);

        usort($invoices, function ($a, $b) {
            return $a['transaction_date'] <=> $b['transaction_date'];
        });

        // get all credit notes and convert to array
        $creditNotes = CreditNote::query()
            ->orderBy('created_at', 'desc')
            ->whereHas('invoice', function ($query) use ($tenancyAgreement) {
                $query->where('tenancy_agreement_id', '=', $tenancyAgreement->id);
            })
            ->select(['id', 'invoice_id', 'created_at as transaction_date','amount_credited as amount'])
            ->selectRaw("concat('CRN #', id,'. ', name,'. Issued on ') as transaction, concat('credit_note') as transaction_type")
            ->where('is_confirmed', '=', true)
            ->get()
            ->toArray();

        // get all the debit notes and convert to array
//        $debitNotes = DebitNote::query()
//            ->orderBy('created_at', 'desc')
//            ->whereHas('invoice', function ($query) use ($tenancyAgreement) {
//                $query->where('tenancy_agreement_id', '=', $tenancyAgreement->id);
//            })
//            ->select(['id', 'created_at as transaction_date','amount_debited as amount'])
//            ->selectRaw("concat('DBN #', id,'. ', name,'. Issued on ') as transaction, concat('debit_note') as transaction_type")
//            ->get()
//            ->toArray();

        // get all invoice payments
        $invoicePayments = InvoicePayment::query()
            ->orderBy('payment_date', 'desc')
            ->where(function ($query) use ($tenancyAgreement) {
                $query->where('tenant_id', '=', $tenancyAgreement->tenant_id);
            })
            ->select(['id', 'invoice_id', 'payment_date as transaction_date','amount'])
            ->selectRaw("concat('PMT #', id,'. Paid on ') as transaction, concat('payment') as transaction_type")
            ->where('is_confirmed', '=', true)
            ->get()
            ->toArray();

        // define the

        // merge the three arrays and group them by invoice id
        $transactions = array_merge($invoices, $creditNotes, $invoicePayments);

        // group the array based on invoice_id and sort them by transaction date
//        $transactions = collect($transactions)->groupBy('invoice_id')->toArray();

        // sort the array by transaction date
        usort($transactions, function ($a, $b) {
            // sort by invoice id then transaction date
            return $a['invoice_id'] <=> $b['invoice_id'] ?: $a['transaction_date'] <=> $b['transaction_date'];
//            return $a['transaction_date'] <=> $b['transaction_date'];
        });

        // obtain the balance carried forward
        $balanceCarriedForward = $tenancyAgreement->balance_carried_forward;

        // obtain the total due
//        $amountDue = $balanceCarriedForward;
//        foreach ($transactions as $transaction) {
//            if ($transaction['transaction_type'] == 'invoice') {
//                $amountDue += $transaction['amount'];
//            } elseif ($transaction['transaction_type'] == 'credit_note') {
//                $amountDue -= $transaction['amount'];
//            } elseif ($transaction['transaction_type'] == 'payment') {
//                $amountDue -= $transaction['amount'];
//            }
//        }

        Log::error($transactions);
//        dd("End of the function");

        try {
            $statementOfAccountItems = '';
//            $statementOfAccountItems = '
//                <tr style="height: 30px;">
//                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="2" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::createFromFormat('Y-m-d',$tenancyAgreement->start_date)->format('M j, Y').'</td>
//                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="3" style="text-align: left; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000;  color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">Balance Forward</td>
//                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.number_format($balanceCarriedForward,2).'</td>
//                    <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format(0,2).'</td>
//                </tr>';

            $propertyName = $tenancyAgreement->property->name;

            $unitName = $tenancyAgreement->unit->name;
            $runningBalance = $balanceCarriedForward;
            $runningAmountDue = 0;

//            foreach ($transactions as $transaction) {
//                $signOfTransaction = $transaction['transaction_type'] == 'invoice' ? '+' : '-';
//                $runningBalance += $transaction['transaction_type'] == 'invoice' ? $amountDue - $transaction['amount'] : $amountDue + $transaction['amount'];
//                $statementOfAccountItems .= '
//                    <tr style="height: 30px;">
//                        <td class="s8b" dir="ltr" colspan="1" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::createFromFormat('Y-m-d H:i:s',$transaction['transaction_date'])->format('F j, Y').'</td>
//                        <td class="s8" dir="ltr" colspan="4" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: center; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$transaction['transaction'].'</td>
//                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: right; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$signOfTransaction.number_format($transaction['amount'],2).'</td>
//                        <td class="s8" dir="ltr" colspan="1" style="border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; text-align: right; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.number_format($runningBalance,2).'</td>
//                    </tr>';
//            }
//            dd($transactions);
            foreach ($transactions as $transaction) {
                $signOfTransaction = $transaction['transaction_type'] == 'invoice' ? '' : '-';
//                $runningBalance += $transaction['transaction_type'] == 'invoice' ? $runningAmountDue + $transaction['amount'] : $runningAmountDue - $transaction['amount'];
                $runningBalance = array_key_exists('unpaid_amount',$transaction) ?  number_format($transaction['unpaid_amount'],2) : "";
                $statementOfAccountItems .= '
                    <tr style="height: 30px;">
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="2" style="font-size: 8pt;border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; border-left-width: 1px; border-left-color: #000; text-align: center; color: #000; font-family: serif; font-size: 8pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.Carbon::parse($transaction['transaction_date'])->format('M j, Y').'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="3" style="text-align: left; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000;  color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: normal; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$transaction['transaction'].'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 3px; padding-left: 3px;">'.$signOfTransaction.number_format($transaction['amount'],2).'</td>
                        <td class="s_cell_with_right_left_border" dir="ltr" colspan="1" style="text-align:right; border-bottom-width: 1px; border-bottom-color: #000; border-right-width: 1px; border-right-color: #000; color: #000; font-family: serif; font-size: 9pt; vertical-align: middle; word-wrap: break-word; white-space: nowrap; direction: ltr; padding-top: 2px; padding-bottom: 2px; padding-right: 0px; padding-left: 3px;">'.$runningBalance.'</td>
                    </tr>';
            }

            Log::error("The number of transactions" . sizeof($transactions));

            // check if the statement of account items can fit in one page,
            // if they are more than one page, then add a page break
            // if less than one page, padd the table with empty rows
            $statementOfAccountItems .= str_repeat(
                '
                        <tr style="height: 30px;">
                            <td class="s_cell_with_right_left_border" colspan="2"></td>
                            <td class="s_cell_with_right_left_border" colspan="3"></td>
                            <td class="s_cell_with_right_left_border" colspan="1"></td>
                            <td class="s_cell_with_right_left_border" colspan="1"></td>
                        </tr>',
                (19 * ((sizeof($transactions) / 19) + 1)) - sizeof($transactions)
            );

            $current = 0;
            $oneToThirtyPastDue = 0;
            $thirtyOneToSixtyPastDue = 0;
            $sixtyOneToNinetyPastDue = 0;
            $overNinetyPastDue = 0;
            // iterate over the invoices obtaining the current, 1-30, 31-60, 61-90, over 90
            foreach ($invoices as $invoice) {
                $invoiceDueDate = Carbon::createFromFormat('Y-m-d',$invoice['invoice_due_date']);
                $daysDifference = $invoiceDueDate->diffInDays(Carbon::now());
                if ($daysDifference > 90){
                    $overNinetyPastDue += $invoice['unpaid_amount'];
                } elseif ($daysDifference > 60){
                    $sixtyOneToNinetyPastDue += $invoice['unpaid_amount'];
                } elseif ($daysDifference > 30){
                    $thirtyOneToSixtyPastDue += $invoice['unpaid_amount'];
                } elseif ($daysDifference > 0 ){
                    $oneToThirtyPastDue += $invoice['unpaid_amount'];
                } elseif ($daysDifference == 0){
                    $current += $invoice['unpaid_amount'];
                }
            }

            // include balance carried forward in the days due calculation
//            if($balanceCarriedForward != 0){
            if(0 != 0){ // commented this out to remove the balance carried forward from statement (invoice will handle it's visibility)
                $balForwardCreation = Carbon::createFromFormat('Y-m-d',$tenancyAgreement['created_at']->toDateString());
                $balForwardDaysDiff = $balForwardCreation->diffInDays(Carbon::now());

                if ($balForwardDaysDiff > 90){
                    $overNinetyPastDue += $balanceCarriedForward;
                } elseif ($balForwardDaysDiff > 60){
                    $sixtyOneToNinetyPastDue += $balanceCarriedForward;
                } elseif ($balForwardDaysDiff > 30){
                    $thirtyOneToSixtyPastDue += $balanceCarriedForward;
                } elseif ($balForwardDaysDiff > 0 ){
                    $oneToThirtyPastDue += $balanceCarriedForward;
                } elseif ($balForwardDaysDiff == 0){
                    $current += $balanceCarriedForward;
                }
            }

            $amountDue = $current + $oneToThirtyPastDue + $thirtyOneToSixtyPastDue + $sixtyOneToNinetyPastDue + $overNinetyPastDue;

            $company = CompanyDetails::latest()->first();
            if (!$company) {
                Notification::make()
                    ->title('Error')
                    ->body('Company details not found')
                    ->danger()
                    ->send();
                return;
            }

            $detailsArray = [
                'customerName' => $unitName.' '.$tenancyAgreement->tenant->name,
                'propertyName' => $propertyName,
                'dateGenerated'=> Carbon::now()->format('M j, Y'),
                'logoUrl'=>'file://'.storage_path('/app/public/'.$company->logo),
                'amountDue' => number_format($amountDue,2),
                'amountEnc' => number_format(0,2),
                'statementOfAccountItemsHTML' => $statementOfAccountItems,
                'current'=> number_format($current,2),
                'oneToThirtyPastDue'=> number_format($oneToThirtyPastDue,2),
                'thirtyOneToSixtyPastDue'=> number_format($thirtyOneToSixtyPastDue,2),
                'sixtyOneToNinetyPastDue'=> number_format($sixtyOneToNinetyPastDue,2),
                'overNinetyPastDue'=> number_format($overNinetyPastDue,2),
                'companyLocation' => $company->location,
                'companyAddress' => $company->address,
                'companyPhone' => $company->phone,
                'companyEmail' => $company->email,
            ];

            $content = File::get(resource_path('documents/templates/statement-of-account-output-document-version-2.html'));

            foreach ($detailsArray as $key => $value) {
                $content = str_replace("@#$key", $value, $content);
            }

            $pdfName = "Statement of Account".
                '_for_' .
                $tenancyAgreement->tenant->name . '_' .
                $propertyName .
                '_for_unit_' .
                $unitName .
                '_'.$tenancyAgreement->id;

            // get the path but without the clatter file system
            $pdfPath = Storage::path('statements_of_account') . '/' . $pdfName . '.pdf';

//            Storage::url($pdfPath);

//            Storage::put($pdfPath, $content);
            $snappy = App::make('snappy.pdf');
            $snappy->setOption('enable-local-file-access', true);
            $snappy->setOption('disable-smart-shrinking', false);
            $snappy->setOption('margin-bottom', '1in');
            $snappy->setOption('margin-left', '1in');
            $snappy->setOption('margin-right', '1in');
            $snappy->setOption('margin-top', '1in');
//            $snappy->generateFromHtml($content, Storage::url($pdfPath));
//            Pdf::generateFromHtml($content, Storage::url($pdfPath));
            $snappy->generateFromHtml($content, $pdfPath);

//            \Barryvdh\DomPDF\PDF::loadView('documents.templates.invoice-output-document', $detailsArray)->save($pdfPath);
//            \Barryvdh\DomPDF\Facade\Pdf::loadView('documents.templates.invoice-output-document', $detailsArray)->save($pdfPath);
//            $pdf = SnappyPdf::loadView('documents.templates.invoice-output-document', $detailsArray)->output();
//            Log::error("Ndio hii: " . $pdf);
//            $pdf = SnappyPdf::loadView('documents.templates.invoice-output-document', $detailsArray);
//            $pdf = SnappyPdf::loadView('documents.templates.invoice-output-document', $detailsArray)->save($pdfPath);
//            $pdf = SnappyPdf::loadHTML($content)->output();
            Log::error("Ndio hii: " . $pdfPath);
//            Storage::put($pdfPath, $pdf->output());

//            Log::info(Storage::url($pdfPath));

            Log::info('--------------------------------------------------------------------------');

            // trigger download of the file then delete it
            return response()->download($pdfPath)->deleteFileAfterSend(true);

            // check if file exists
//            if (file_exists($pdfPath)) {
//                $savedPath = explode('/', $pdfPath);
//                // retrieve the string after the string 'app'
//                foreach ($savedPath as $key => $value) {
//                    if ($value == 'app') {
//                        $savedPath = implode("/", array_slice($savedPath, $key + 1));
//                        break;
//                    }
//                }
////                $this->is_generated = 1;
////                $this->document_url = $savedPath;
////                $this->updated_by = auth()->user()->id;
////
////                $this->save();
//
//                return true;
//            } else {
//                return false;
//            }
        }catch (\Exception $exception){
            Log::error($exception->getTraceAsString());
            Log::error($exception->getMessage());
            return false;
        }
    }
}
