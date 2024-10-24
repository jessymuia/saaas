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
