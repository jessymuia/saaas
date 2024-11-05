<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Filament\Exports\InvoiceExporter;
use App\Models\InvoicePayment;
use App\Rules\CheckPaidAmountDoesNotExceedAmountDue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CompanyDetails;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoicePaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'invoicePayments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('payment_type_id')
                    ->required()
                    ->relationship('paymentType', 'type'),
                Forms\Components\DateTimePicker::make('payment_date')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
//                    ->rule(fn (Get $get) => new CheckPaidAmountDoesNotExceedAmountDue($get('invoice_id'))),
//                    ->rule(fn (Get $get) => new CheckPaidAmountDoesNotExceedAmountDue($this->ownerRecord->id)),
                Forms\Components\TextInput::make('paid_by')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_reference')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Additional Information')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_id')
                    ->label('Invoice ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice.tenancyAgreement.property.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice.tenancyAgreement.unit.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice.tenancyAgreement.tenant.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentType.type')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receivedBy.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_reference')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_confirmed')
                    ->boolean()
                    ->label('Confirmed?'),
                Tables\Columns\TextColumn::make('document_generated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_generated_by.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
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
                    ->mutateFormDataUsing(function ($data){
                        $data['tenant_id'] = $this->ownerRecord->tenancyAgreement->tenant_id;
                        $data['created_by'] = auth()->id();
                        $data['received_by'] = auth()->id();

                        return $data;
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(function (InvoicePayment $record) { // only visible if receipt has been confirmed
                        return !$record->is_confirmed;
                    })
                    ->mutateFormDataUsing(function ($data){
                        $data['updated_by'] = auth()->id();

                        return $data;
                    }),
                Tables\Actions\Action::make('confirm-invoice-payment')
                    ->label("Confirm")
                    ->action(function(InvoicePayment $record){
                        // update the is_confirmed label of this Invoice Payment
                        $isUpdated = $record->update(['is_confirmed' => true]);

                        if ($isUpdated){
                            Notification::make()
                                ->title('Invoice Payment Confirmed')
                                ->success()
                                ->send();
                        }else{
                            Notification::make()
                                ->danger()
                                ->title('Invoice Payment Confirmation Failed')
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('generate-receipt')
                    ->label("Generate Receipt")
                    ->icon('heroicon-o-document-text')
                    ->action(function(InvoicePayment $record){
                        // get the invoice payment
                        $invoicePayment = InvoicePayment::find($record->id);
                        $invoicePayment->generateInvoicePaymentReceipt();
                    })
                    ->visible(fn (InvoicePayment $record) => strtotime($record->document_generated_at) === false),
                // action to preview the receipt document
                Tables\Actions\Action::make('preview-receipt')
                    ->label('Preview Receipt')
                    ->icon('heroicon-o-document-text')
                    ->action(function (InvoicePayment $record) {
                        // get the invoice payment
                        $filename = str_replace('invoice_payments/', '', $record->document_path);
                        // preview invoice payment document
                        return redirect()
                            ->route('preview.receipt', ['receipt' => $filename]);
                    })
                    ->disabled(function (InvoicePayment $record) { // only visible if receipt has been confirmed
                        return strtotime($record->document_generated_at) === false;
                    }),
                    Tables\Actions\Action::make('generatePdf')
                    ->label('Generate PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->action(function (InvoicePayment $record) {
                        try {
                            // Load the payment with its relationships
                            $payment = $record->load([
                                'invoice',
                                'paymentType',
                                'receivedBy',
                                'tenant',
                                'client',
                                'propertyOwner'
                            ]);
                
                            $company = CompanyDetails::latest()->first();
                            if (!$company) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Company details not found')
                                    ->danger()
                                    ->send();
                                return;
                            }
                
                            // Get recipient details
                            $recipientName = $payment->tenant_id ? $payment->tenant->name : 
                                           ($payment->client_id ? $payment->client->name : 
                                           ($payment->property_owner_id ? $payment->propertyOwner->name : 'N/A'));
                            
                            $recipientAddress = $payment->tenant_id ? $payment->tenant->address : 
                                              ($payment->client_id ? $payment->client->address : 
                                              ($payment->property_owner_id ? $payment->propertyOwner->address : 'N/A'));
                
                            // Format dates
                            $paymentDate = \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y');
                            $receiptDate = \Carbon\Carbon::parse($payment->created_at)->format('F j, Y');
                
                            $data = [
                                'payment' => $payment,
                                'company' => $company,
                                'recipientName' => $recipientName,
                                'recipientAddress' => $recipientAddress,
                                'paymentDate' => $paymentDate,
                                'receiptDate' => $receiptDate,
                                'logoData' => $company->logo ? base64_encode(file_get_contents(storage_path('app/public/' . $company->logo))) : null,
                                'logoExtension' => $company->logo ? pathinfo(storage_path('app/public/' . $company->logo), PATHINFO_EXTENSION) : null,
                            ];
                
                            $pdf = Pdf::loadView('pdfs.invoice-payment', $data);
                            $pdf->setPaper('A4', 'portrait');
                            
                            // Set additional PDF options
                            $pdf->setOption('isPhpEnabled', true);
                            $pdf->setOption('isRemoteEnabled', true);
                            $pdf->setOption('isHtml5ParserEnabled', true);
                
                            return response()->streamDownload(
                                function () use ($pdf) {
                                    echo $pdf->output();
                                }, 
                                "invoice-payment-{$payment->id}.pdf", 
                                [
                                    'Content-Type' => 'application/pdf',
                                    'Content-Disposition' => 'attachment'
                                ]
                            );
                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error generating PDF')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            
                            \Log::error('PDF Generation Error:', [
                                'error' => $e->getMessage(),
                                'payment_id' => $record->id,
                                'stack_trace' => $e->getTraceAsString()
                            ]);
                        }
                    }),
                // action to send the receipt document
                Tables\Actions\Action::make('send-receipt')
                    ->label('Send Receipt')
                    ->icon('heroicon-o-envelope')
                    ->action(function (InvoicePayment $record) {
                        // send the invoice payment mail
                        $record->sendInvoicePaymentEmail();
                    })
                    ->disabled(function (InvoicePayment $record) { // only visible if receipt has been confirmed
                        return strtotime($record->document_generated_at) === false
                            || strtotime($record->document_sent_at) !== false;
                    }),
                
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(InvoiceExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(InvoiceExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }
}
